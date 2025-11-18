<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
        try {
            // ========== STEP 1: Log the raw request ==========
            Log::info('🟡 Incoming store() request data', [
                'all' => $request->all(),
                'files' => $request->allFiles(),
            ]);

            // ========== STEP 2: Validate ==========
            $validated = $request->validate([
                'title'             => 'required|string|max:255',
                'slug'              => 'required|string|max:255|unique:services,slug',
                'brief_description' => 'required|string',
                'thumbnail'         => 'required|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
                'contents'          => 'required|array',
                'contents.*.type'   => 'required|string|in:heading,paragraph,list,image,feature,features',
            ]);

            // ========== STEP 3: Log validation success ==========
            Log::info('✅ Validation successful', ['validated' => $validated]);

            // ========== STEP 4: Start transaction ==========
            return DB::transaction(function () use ($request, $validated) {
                // Upload thumbnail
                if ($request->hasFile('thumbnail')) {
                    $validated['thumbnail'] = $request->file('thumbnail')
                        ->store('services/thumbnails', 'public');
                }

                // Create main Service
                $service = Service::create([
                    'title'             => $validated['title'],
                    'slug'              => $validated['slug'],
                    'brief_description' => $validated['brief_description'],
                    'thumbnail'         => $validated['thumbnail'] ?? null,
                ]);

                Log::info('🟢 Service created', ['id' => $service->id]);

                // Save Service Contents
                foreach ($request->contents as $i => $content) {
                    try {
                        $data = [
                            'service_id' => $service->id,
                            'type'       => $content['type'] ?? null,
                            'position'   => $content['position'] ?? $i + 1,
                            'content'    => null,
                        ];

                        $type = $content['type'] ?? null;
                        if ($type === 'features') {
                            $content['type'] = 'feature';
                            $type = 'feature';
                        }

                        switch ($type) {
                            case 'heading':
                            case 'paragraph':
                                $data['content'] = $content['content'] ?? '';
                                break;

                            case 'list':
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

                            default:
                                Log::warning('⚠ Unknown content type', ['content' => $content]);
                                break;
                        }

                        ServiceContent::create($data);
                        Log::info("🟢 Content #{$i} saved", ['type' => $content['type']]);
                    } catch (\Throwable $innerEx) {
                        Log::error("❌ Error saving content #{$i}", [
                            'content' => $content,
                            'message' => $innerEx->getMessage(),
                            'trace' => $innerEx->getTraceAsString(),
                        ]);
                    }
                }

                Log::info('✅ All contents processed successfully');

                return redirect()
                    ->back()
                    ->with('success', 'Service created successfully.');
            });
        } catch (ValidationException $ex) {
            Log::error('❌ Validation failed in store()', [
                'errors' => $ex->errors(),
                'input' => $request->all(),
            ]);
            return back()->withErrors($ex->errors())->withInput();
        } catch (\Throwable $ex) {
            Log::error('💥 Exception in store()', [
                'message' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString(),
            ]);

            if (app()->environment('local')) {
                return back()->with('error', $ex->getMessage());
            }

            return back()->with('error', 'An unexpected error occurred.');
        }
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
            'type' => 'required|in:heading,paragraph,list,image,feature,features',
            'content' => 'required',
            'position' => 'nullable|integer|min:1',
        ]);

        $type = $validated['type'] === 'features' ? 'feature' : $validated['type'];
        $contentData = $validated['content'];

        // Handle image uploads
        if ($type === 'image' && $request->hasFile('content')) {
            $contentData = $request->file('content')->store('services/content', 'public');
        }

        // Handle lists (array)
        if ($type === 'list' && is_array($contentData)) {
            $contentData = array_filter($contentData); // Remove empty items
        }

        if ($type === 'feature' && is_array($contentData)) {
            $contentData = [
                'heading'   => $contentData['heading'] ?? '',
                'paragraph' => $contentData['paragraph'] ?? '',
            ];
        }

        ServiceContent::create([
            'service_id' => $service->id,
            'type' => $type,
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

        if ($content->type === 'feature' && is_array($contentData)) {
            $contentData = [
                'heading'   => $contentData['heading'] ?? '',
                'paragraph' => $contentData['paragraph'] ?? '',
            ];
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
