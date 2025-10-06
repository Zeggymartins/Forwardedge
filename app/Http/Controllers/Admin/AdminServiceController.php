<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()->get();
        return view('admin.pages.services.view_service', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'slug'              => 'required|string|max:255|unique:services,slug',
            'brief_description' => 'required|string',
            'thumbnail'         => 'required|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
            'contents'          => 'required|array',
            'contents.*.type'   => 'required|string|in:heading,paragraph,list,image,feature',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $validated['thumbnail'] = $request->file('thumbnail')
                    ->store('services/thumbnails', 'public');
            }

            // Create the service
            $service = Service::create([
                'title'             => $validated['title'],
                'slug'              => $validated['slug'],
                'brief_description' => $validated['brief_description'],
                'thumbnail'         => $validated['thumbnail'] ?? null,
            ]);

            // Save service contents
            foreach ($request->contents as $content) {
                $data = [
                    'service_id' => $service->id,
                    'type'       => $content['type'],
                    'position'   => $content['position'] ?? null,
                    'content'    => null, // default
                ];

                switch ($content['type']) {
                    case 'heading':
                    case 'paragraph':
                        $data['content'] = $content['content'] ?? '';
                        break;

                    case 'list':
                        // always encode as JSON
                        $data['content'] = json_encode($content['content'] ?? []);
                        break;

                    case 'image':
                        $images = [];
                        if (isset($content['content']) && is_array($content['content'])) {
                            foreach ($content['content'] as $img) {
                                if ($img instanceof \Illuminate\Http\UploadedFile) {
                                    $images[] = $img->store('services/images', 'public');
                                }
                            }
                        }
                        $data['content'] = json_encode($images);
                        break;

                    case 'feature':
                        $featureData = [
                            'heading'   => $content['content']['heading'] ?? '',
                            'paragraph' => $content['content']['paragraph'] ?? '',
                        ];
                        $data['content'] = json_encode($featureData);
                        break;
                }

                // Save each content block
                ServiceContent::create($data);
            }

            return redirect()
                ->route('services.add')
                ->with('success', 'Service created successfully.');
        });
    }

    public function show($id)
    {
        $service = Service::with('contents')->findOrFail($id);
        return view('admin.pages.services.service_details', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:services,slug,' . $id,
            'brief_description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($service->thumbnail) {
                Storage::disk('public')->delete($service->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('services/thumbnails', 'public');
        }

        $service->update($validated);

        return back()->with('success', 'Service updated successfully!');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        // Delete thumbnail
        if ($service->thumbnail) {
            Storage::disk('public')->delete($service->thumbnail);
        }

        // Delete all contents
        foreach ($service->contents as $content) {
            if (isset($content->content['image'])) {
                Storage::disk('public')->delete($content->content['image']);
            }
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully!');
    }

    // Service Content Management
    public function storeContent(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);

        $validated = $request->validate([
            'type' => 'required|in:heading,paragraph,list,image,feature',
            'content' => 'required',
            'position' => 'nullable|integer|min:1',
        ]);

        $contentData = $validated['content'];

        // Handle image uploads
        if ($validated['type'] === 'image' && $request->hasFile('content')) {
            $contentData = $request->file('content')->store('services/content', 'public');
        }

        // Handle lists (array)
        if ($validated['type'] === 'list' && is_array($contentData)) {
            $contentData = array_filter($contentData); // Remove empty items
        }

        ServiceContent::create([
            'service_id' => $service->id,
            'type' => $validated['type'],
            'content' => $contentData,
            'position' => $validated['position'] ?? ($service->contents()->max('position') + 1),
        ]);

        return back()->with('success', 'Content added successfully!');
    }

    public function updateContent(Request $request, $serviceId, $contentId)
    {
        $content = ServiceContent::where('service_id', $serviceId)
            ->findOrFail($contentId);

        $validated = $request->validate([
            'content' => 'required',
            'position' => 'nullable|integer|min:1',
        ]);

        $contentData = $validated['content'];

        // Handle image uploads
        if ($content->type === 'image' && $request->hasFile('content')) {
            // Delete old image
            if (is_string($content->content)) {
                Storage::disk('public')->delete($content->content);
            }
            $contentData = $request->file('content')->store('services/content', 'public');
        }

        // Handle lists
        if ($content->type === 'list' && is_array($contentData)) {
            $contentData = array_filter($contentData);
        }

        $content->update([
            'content' => $contentData,
            'position' => $validated['position'] ?? $content->position,
        ]);

        return back()->with('success', 'Content updated successfully!');
    }

    public function destroyContent($serviceId, $contentId)
    {
        $content = ServiceContent::where('service_id', $serviceId)
            ->findOrFail($contentId);

        // Delete image if exists
        if ($content->type === 'image' && is_string($content->content)) {
            Storage::disk('public')->delete($content->content);
        }

        $content->delete();

        return back()->with('success', 'Content deleted successfully!');
    }
}
