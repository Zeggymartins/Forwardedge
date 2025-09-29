<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ServiceController extends Controller
{
    public function ServiceList()
    {
        $services = Service::all();

        return view('user.pages.service', compact('services'));
    }


    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->with(['contents' => function ($query) {
                $query->orderBy('position', 'asc');
            }])
            ->firstOrFail();

        $otherServices = Service::where('id', '!=', $service->id)->get();

        return view('user.pages.service_details', compact('service', 'otherServices'));
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
}
