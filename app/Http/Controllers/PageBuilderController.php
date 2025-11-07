<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{Page, Block, Course, Event, CourseSchedule};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{DB, Storage, Log, Route as RouteFacade};
use App\Support\PageBlueprint;

class PageBuilderController extends Controller
{
    /* ===================== PUBLIC ===================== */

    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)
            ->when(app()->environment('production'), fn($q) => $q->where('status', 'published'))
            ->with(['blocks' => fn($q) => $q->where('is_published', true)->orderBy('order')])
            ->firstOrFail();

        return view('user.pages.dynamic', compact('page'));
    }

    /* ===================== ADMIN: PAGES ===================== */

    public function adminPages()
    {
        $pages   = Page::with(['pageable'])->latest('updated_at')->paginate(20);
        $courses = Course::orderBy('title')->get(['id', 'title']);
        $events  = Event::orderBy('title')->get(['id', 'title']);

        return view('admin.pages.page_builder.index', compact('pages', 'courses', 'events'));
    }

    public function createPage()
    {
        $courses = Course::orderBy('title')->get(['id', 'title']);
        $events  = Event::orderBy('title')->get(['id', 'title']);

        return view('admin.pages.page_builder.create', compact('courses', 'events'));
    }

    public function storePage(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'slug'       => 'nullable|string|max:255|unique:pages,slug',
            'status'     => 'required|string|in:draft,published',
            'owner_type' => 'nullable|string|in:course,event',
            'owner_id'   => 'nullable|integer',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        if (!empty($data['owner_type']) && !empty($data['owner_id'])) {
            $pageableClass = match ($data['owner_type']) {
                'course' => Course::class,
                'event'  => Event::class,
                default  => null,
            };

            if ($pageableClass && $pageableClass::find($data['owner_id'])) {
                $data['pageable_type'] = $pageableClass;
                $data['pageable_id']   = $data['owner_id'];
            }
        }

        unset($data['owner_type'], $data['owner_id']);

        $page = Page::create($data);

        return redirect()->route('pb.blocks', $page)->with('success', 'Page created successfully.');
    }

    public function editPage(Page $page)
    {
        $courses = Course::orderBy('title')->get(['id', 'title']);
        $events  = Event::orderBy('title')->get(['id', 'title']);

        return view('admin.pages.page_builder.edit', compact('page', 'courses', 'events'));
    }

    public function updatePage(Request $request, Page $page)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'slug'       => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'status'     => 'required|string|in:draft,published',
            'owner_type' => 'nullable|string|in:course,event',
            'owner_id'   => 'nullable|integer',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        if (!empty($data['owner_type']) && !empty($data['owner_id'])) {
            $pageableClass = match ($data['owner_type']) {
                'course' => Course::class,
                'event'  => Event::class,
                default  => null,
            };

            if ($pageableClass && $pageableClass::find($data['owner_id'])) {
                $data['pageable_type'] = $pageableClass;
                $data['pageable_id']   = $data['owner_id'];
            } else {
                $data['pageable_type'] = null;
                $data['pageable_id']   = null;
            }
        } else {
            $data['pageable_type'] = null;
            $data['pageable_id']   = null;
        }

        unset($data['owner_type'], $data['owner_id']);

        $page->update($data);

        return back()->with('success', 'Page updated successfully.');
    }

    public function destroyPage(Page $page)
    {
        $page->delete();
        return redirect()->route('pb.pages')->with('success', 'Page deleted successfully.');
    }

    /* ===================== ADMIN: BLOCKS ===================== */

    public function adminBlocks(Page $page)
    {
        $blocks = $page->blocks()
            ->orderBy('order')
            ->get()
            ->map(function ($b) {
                $b->data = is_array($b->data) ? $b->data : json_decode($b->data ?? '{}', true);
                return $b;
            });

        $blockTypes = PageBlueprint::allowedTypes();

        $variants = [
            'hero'         => ['default', 'minimal', 'with_video'],
            'hero2'        => ['default'],
            'hero3'        => ['default'],
            'hero4'        => ['default'],
            'overview'     => ['cards', 'timeline'],
            'overview2'    => ['default'],
            'form_dark'    => ['default'],
            'form_light'   => ['default'],
            'about'        => ['default', 'split'],
            'about2'       => ['default'],
            'sections'     => ['default', 'carousel'],
            'sections2'    => ['default'],
            'marquees'     => ['default'],
            'logo_slider'  => ['default'],
            'gallary'      => ['grid', 'masonry'],
            'testimonial'  => ['slider', 'grid', 'featured'],
            'pricing'      => ['default', 'comparison'],
            'faq'          => ['accordion', 'tabs'],
            'closing_cta'  => ['default', 'split'],
        ];

        $allowedRouteParams = ['course', 'schedule', 'event', 'event_id'];

        $internalRoutes = collect(RouteFacade::getRoutes())
            ->filter(fn($route) => $route->getName() && in_array('GET', $route->methods()))
            ->reject(function ($route) {
                $uri = ltrim($route->uri(), '/');
                $name = $route->getName();

                return Str::startsWith($uri, 'ctrl-panel-v2')
                    || Str::startsWith($uri, 'admin')
                    || Str::startsWith($name, 'admin.')
                    || Str::startsWith($name, 'pb.')
                    || Str::contains($name, 'admin');
            })
            ->map(function ($route) {
                $uri  = $route->uri();
                $path = '/' . ltrim($uri, '/');
                if ($path === '//') {
                    $path = '/';
                }

                preg_match_all('/\{([^}]+)\}/', $uri, $matches);
                $placeholders = $matches[1] ?? [];

                return [
                    'name'         => $route->getName(),
                    'uri'          => $uri,
                    'path'         => $path,
                    'needs_params' => !empty($placeholders),
                    'placeholders' => $placeholders,
                ];
            })
            ->filter(function ($route) use ($allowedRouteParams) {
                if (empty($route['placeholders'])) {
                    return false;
                }

                return collect($route['placeholders'])
                    ->every(fn($ph) => in_array($ph, $allowedRouteParams, true));
            })
            ->sortBy('name')
            ->values();

        $routeBindingOptions = $this->buildRouteBindingOptions();

        return view('admin.pages.page_builder.blocks.form', compact(
            'page',
            'blocks',
            'blockTypes',
            'variants',
            'internalRoutes',
            'routeBindingOptions'
        ));
    }

    public function storeBlock(Request $request, Page $page)
    {
        $type = (string) $request->input('type');
        PageBlueprint::ensureValidType($type);

        // Get and clean data
        $rawData = $request->input('data', []);
        $cleanedData = $this->cleanBlockData($rawData, $type);

        // Merge back for validation
        $request->merge(['data' => $cleanedData]);

        // Validate
        $validated = $request->validate(array_merge(
            PageBlueprint::prefixedRulesFor($type),
            [
                'variant'      => 'nullable|string|max:100',
                'is_published' => 'nullable|boolean',
            ]
        ));

        $payload = $validated['data'] ?? [];

        // Handle file uploads
        $payload = $this->handleBlockFileUploads($request, $payload, $type);

        // Create block
        $nextOrder = ((int) $page->blocks()->max('order')) + 10;

        $page->blocks()->create([
            'type'         => $type,
            'variant'      => $request->input('variant'),
            'data'         => $payload,
            'order'        => $nextOrder,
            'is_published' => (bool) $request->boolean('is_published', true),
        ]);

        return redirect()
            ->route('pb.blocks', $page)
            ->with('success', "Block '{$type}' created successfully.");
    }

    public function updateBlock(Request $request, Block $block)
    {
        $type = (string) $request->input('type');
        PageBlueprint::ensureValidType($type);

        // Get and clean data
        $rawData = $request->input('data', []);
        $cleanedData = $this->cleanBlockData($rawData, $type);

        // Merge back for validation
        $request->merge(['data' => $cleanedData]);

        // Validate
        $validated = $request->validate(array_merge(
            PageBlueprint::prefixedRulesFor($type),
            [
                'variant'      => 'nullable|string|max:100',
                'is_published' => 'nullable|boolean',
            ]
        ));

        // Merge with existing data to preserve unchanged fields
        $payload = array_merge(
            is_array($block->data) ? $block->data : [],
            $validated['data'] ?? []
        );

        // Handle file uploads
        $payload = $this->handleBlockFileUploads($request, $payload, $type, $block->data);

        // Update block
        $block->update([
            'type'         => $type,
            'variant'      => $request->input('variant'),
            'data'         => $payload,
            'is_published' => (bool) $request->boolean('is_published', $block->is_published),
        ]);

        return redirect()
            ->route('pb.blocks', $block->page)
            ->with('success', "Block '{$type}' updated successfully.");
    }

    public function destroyBlock(Block $block)
    {
        $this->deleteBlockFiles($block->data ?? []);
        $page = $block->page;
        $block->delete();

        return redirect()->route('pb.blocks', $page)->with('success', 'Block deleted.');
    }

    public function reorderBlocks(Request $request, Page $page)
    {
        $order = $request->input('order');

        DB::transaction(function () use ($order, $page) {
            // Case A: [{id: 123, order: 10}, {id: 456, order: 20}]
            if (is_array($order) && isset($order[0]) && is_array($order[0]) && array_key_exists('id', $order[0])) {
                foreach ($order as $row) {
                    $id  = $row['id'] ?? null;
                    $ord = (int) ($row['order'] ?? 0);
                    if ($id) {
                        Block::where('page_id', $page->id)->whereKey($id)->update(['order' => $ord ?: 10]);
                    }
                }
                return;
            }

            // Case B: [123, 456, 789] → assign 10,20,30...
            if (is_array($order) && array_is_list($order)) {
                $pos = 10;
                foreach ($order as $id) {
                    Block::where('page_id', $page->id)->whereKey($id)->update(['order' => $pos]);
                    $pos += 10;
                }
                return;
            }

            // Case C: {"123": 10, "456": 20}
            if (is_array($order)) {
                foreach ($order as $id => $ord) {
                    Block::where('page_id', $page->id)->whereKey($id)->update(['order' => (int) $ord]);
                }
            }
        });

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Block order saved successfully.');
    }

    public function json(Page $page)
    {
        $blocks = $page->blocks()
            ->orderBy('order')
            ->get()
            ->map(function ($b) {
                $b->data = is_array($b->data) ? $b->data : json_decode($b->data ?? '{}', true);
                return $b;
            });

        return response()->json($blocks);
    }

    /* ===================== PRIVATE HELPER METHODS ===================== */

    private function buildRouteBindingOptions(): array
    {
        $options = [];

        $courseOptions = Course::query()
            ->orderBy('title')
            ->get(['id', 'title', 'slug'])
            ->map(fn($course) => [
                'value' => (string) $course->id,
                'label' => $course->title ?: "Course #{$course->id}",
                'hint'  => $course->slug ? "Slug: {$course->slug}" : null,
            ])
            ->all();

        $options['course'] = $courseOptions;
        if (!empty($courseOptions)) {
            $options['course_id'] = $courseOptions;
        }

        $options['schedule'] = CourseSchedule::query()
            ->with('course:id,title')
            ->orderBy('start_date')
            ->get(['id', 'course_id', 'start_date', 'location'])
            ->map(function ($schedule) {
                $courseTitle = optional($schedule->course)->title ?: 'Course';
                $startDate   = $schedule->start_date ? $schedule->start_date->format('M j, Y') : 'TBA';
                $label       = trim("{$courseTitle} • {$startDate}");
                $hintParts   = array_filter([
                    $schedule->location ? "Location: {$schedule->location}" : null,
                    "ID #{$schedule->id}",
                ]);

                return [
                    'value' => (string) $schedule->id,
                    'label' => $label,
                    'hint'  => implode(' • ', $hintParts),
                ];
            })
            ->all();

        $eventOptions = Event::query()
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn($event) => [
                'value' => (string) $event->id,
                'label' => $event->title ?: "Event #{$event->id}",
                'hint'  => "ID #{$event->id}",
            ])
            ->all();

        $options['event'] = $eventOptions;
        if (!empty($eventOptions)) {
            $options['event_id'] = $eventOptions;
        }

        $options['page'] = Page::query()
            ->orderBy('title')
            ->get(['id', 'title', 'slug'])
            ->map(fn($page) => [
                'value' => (string) $page->id,
                'label' => $page->title ?: "Page #{$page->id}",
                'hint'  => $page->slug ? "Slug: {$page->slug}" : null,
            ])
            ->all();

        return array_filter($options, fn($list) => !empty($list));
    }

    /**
     * Clean and normalize block data before validation
     */
    private function cleanBlockData(array $data, string $type): array
    {
        // Remove empty items from arrays
        $data = $this->removeEmptyArrayItems($data);

        // Type-specific cleaning
        return match ($type) {
            'pricing'     => $this->cleanPricingData($data),
            'closing_cta' => $this->normalizeClosingCta($data),
            'hero3'       => $this->cleanHero3Data($data),
            'form_dark',
            'form_light'  => $this->normalizeFormFields($data),
            default       => $data,
        };
    }

    /**
     * Recursively remove empty items from arrays
     */
    private function removeEmptyArrayItems(mixed $data): mixed
    {
        if (!is_array($data)) {
            return $data;
        }

        $cleaned = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Recursively clean nested arrays
                $value = $this->removeEmptyArrayItems($value);

                // Check if this is an indexed array
                if (array_is_list($value)) {
                    // Remove empty items from lists
                    $value = array_filter($value, function ($item) {
                        if (is_string($item)) {
                            return trim($item) !== '';
                        }
                        if (is_array($item)) {
                            // For objects, check if any non-empty values exist
                            return !empty(array_filter($item, function ($v) {
                                return is_string($v) ? trim($v) !== '' : !empty($v);
                            }));
                        }
                        return !empty($item);
                    });

                    // Re-index array
                    $value = array_values($value);
                }

                $cleaned[$key] = $value;
            } else {
                $cleaned[$key] = $value;
            }
        }

        return $cleaned;
    }

    /**
     * Clean pricing data - ensure at least one plan with non-empty features
     */
    private function cleanPricingData(array $data): array
    {
        if (!isset($data['plans']) || !is_array($data['plans'])) {
            throw new \InvalidArgumentException('Pricing block requires at least one plan');
        }

        $cleanPlans = [];

        foreach ($data['plans'] as $plan) {
            // Skip plans with no title
            if (empty($plan['title']) || trim((string) $plan['title']) === '') {
                continue;
            }

            // Clean features array
            if (isset($plan['features']) && is_array($plan['features'])) {
                $cleanFeatures = [];
                foreach ($plan['features'] as $feature) {
                    if (!empty($feature) && trim((string) $feature) !== '') {
                        $cleanFeatures[] = trim((string) $feature);
                    }
                }
                $plan['features'] = array_values($cleanFeatures);
            } else {
                $plan['features'] = [];
            }

            // Remove old 'period' field if present (legacy cleanup)
            unset($plan['period']);

            $cleanPlans[] = $plan;
        }

        // Ensure at least one valid plan
        if (empty($cleanPlans)) {
            throw new \InvalidArgumentException('Pricing block requires at least one plan with a title');
        }

        $data['plans'] = array_values($cleanPlans);
        return $data;
    }

    private function normalizeFormFields(array $data): array
    {
        if (!isset($data['fields']) || !is_array($data['fields'])) {
            $data['fields'] = [];
            return $data;
        }

        $normalized = [];

        foreach ($data['fields'] as $field) {
            $label = trim((string) ($field['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $name = trim((string) ($field['name'] ?? '')) ?: $label;
            $slug = Str::slug($name, '_');
            if ($slug === '') {
                $slug = 'field_' . (count($normalized) + 1);
            }

            $type = in_array($field['type'] ?? '', ['text', 'email', 'tel', 'textarea'], true)
                ? $field['type']
                : 'text';

            $normalized[] = [
                'label'       => $label,
                'name'        => $slug,
                'type'        => $type,
                'placeholder' => $field['placeholder'] ?? '',
                'required'    => filter_var($field['required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'width'       => ($field['width'] ?? 'full') === 'half' ? 'half' : 'full',
            ];
        }

        $data['fields'] = array_values($normalized);

        return $data;
    }

    /**
     * Clean hero3 title segments
     */
    private function cleanHero3Data(array $data): array
    {
        // Keep only non-empty strings in title_segments
        if (!empty($data['title_segments']) && is_array($data['title_segments'])) {
            $data['title_segments'] = array_values(array_filter(
                array_map(
                    fn($v) => is_string($v) ? trim($v) : null,
                    $data['title_segments']
                ),
                fn($v) => $v !== null && $v !== ''
            ));
        }

        // Cast highlight_index safely
        if (isset($data['highlight_index'])) {
            $data['highlight_index'] = (int) $data['highlight_index'];
        }

        return $data;
    }

    /**
     * Normalize closing CTA - handle both new and legacy key formats
     */
    private function normalizeClosingCta(array $data): array
    {
        if (!isset($data['ctas']) || !is_array($data['ctas'])) {
            return $data;
        }

        $out = [];
        foreach ($data['ctas'] as $cta) {
            if (!is_array($cta)) continue;

            // Skip completely empty CTAs
            if (
                empty($cta['link_text']) && empty($cta['text']) &&
                empty($cta['link']) && empty($cta['href'])
            ) {
                continue;
            }

            // Map legacy keys to new schema
            $out[] = [
                'link_text' => $cta['link_text'] ?? ($cta['text'] ?? null),
                'link'      => $cta['link'] ?? ($cta['href'] ?? null),
            ];
        }

        $data['ctas'] = array_values($out);
        return $data;
    }

    /**
     * Handle file uploads for all block types
     */
    private function handleBlockFileUploads(
        Request $request,
        array $payload,
        string $type,
        ?array $existingData = null
    ): array {
        $existingData = is_array($existingData) ? $existingData : [];

        // Single-file blocks
        $singleFileBlocks = [
            'hero'   => ['banner_image' => 'blocks/hero'],
            'hero2'  => ['hero_image' => 'blocks/hero2', 'banner_image' => 'blocks/hero2'],
            'hero3'  => ['banner_image' => 'blocks/hero3', 'image' => 'blocks/hero3', 'hero_image' => 'blocks/hero3', 'verified_icon' => 'blocks/hero3'],
            'hero4'  => ['hero_image' => 'blocks/hero4'],
            'about'  => ['banner_left' => 'blocks/about'],
            'about2' => ['about_image' => 'blocks/about2'],
        ];

        if (isset($singleFileBlocks[$type])) {
            foreach ($singleFileBlocks[$type] as $field => $path) {
                if ($request->hasFile($field)) {
                    // Delete old file
                    if (!empty($existingData[$field])) {
                        Storage::disk('public')->delete($existingData[$field]);
                    }
                    $payload[$field] = $request->file($field)->store($path, 'public');
                } elseif (!empty($existingData[$field])) {
                    // Keep existing
                    $payload[$field] = $existingData[$field];
                }
            }
        }

        // Array-based file blocks
        $arrayFileBlocks = [
            'about'      => ['cards' => ['image' => 'blocks/about/cards'], 'tiles' => ['bg' => 'blocks/about/tiles']],
            'sections'   => ['items' => ['image' => 'blocks/sections']],
            'sections2'  => ['items' => ['image' => 'blocks/sections2']],
            'overview2'  => ['items' => ['image' => 'blocks/program_includes']],
            'marquees'   => ['slides' => ['image' => 'blocks/marquees']],
            'gallary'    => ['items' => ['image' => 'blocks/gallery']],
            'testimonial' => ['items' => ['photo' => 'blocks/testimonials']],
            'logo_slider' => ['logos' => ['image' => 'blocks/brands'], 'brands' => ['image' => 'blocks/brands']],
        ];

        if (isset($arrayFileBlocks[$type])) {
            foreach ($arrayFileBlocks[$type] as $arrayKey => $fileFields) {
                if (isset($payload[$arrayKey]) && is_array($payload[$arrayKey])) {
                    foreach ($payload[$arrayKey] as $i => $item) {
                        foreach ($fileFields as $fileField => $path) {
                            if ($request->hasFile("{$arrayKey}.{$i}.{$fileField}")) {
                                // Delete old
                                if (!empty($existingData[$arrayKey][$i][$fileField] ?? null)) {
                                    Storage::disk('public')->delete($existingData[$arrayKey][$i][$fileField]);
                                }
                                $payload[$arrayKey][$i][$fileField] = $request->file("{$arrayKey}.{$i}.{$fileField}")
                                    ->store($path, 'public');
                            } elseif (!empty($existingData[$arrayKey][$i][$fileField] ?? null)) {
                                // Keep existing
                                $payload[$arrayKey][$i][$fileField] = $existingData[$arrayKey][$i][$fileField];
                            }
                        }
                    }
                }
            }
        }

        return $payload;
    }

    /**
     * Delete files associated with a block (recursively)
     */
    private function deleteBlockFiles(?array $data): void
    {
        if (!$data) return;

        $fileKeys = ['banner_image', 'banner_left', 'image', 'bg', 'photo', 'hero_image', 'about_image', 'verified_icon'];

        // Delete top-level files
        foreach ($fileKeys as $key) {
            if (!empty($data[$key]) && is_string($data[$key])) {
                Storage::disk('public')->delete($data[$key]);
            }
        }

        // Delete nested array files
        $nestedKeys = ['cards', 'tiles', 'items', 'slides', 'logos', 'brands'];
        foreach ($nestedKeys as $nestedKey) {
            if (!empty($data[$nestedKey]) && is_array($data[$nestedKey])) {
                foreach ($data[$nestedKey] as $item) {
                    if (is_array($item)) {
                        $this->deleteBlockFiles($item);
                    }
                }
            }
        }
    }
}
