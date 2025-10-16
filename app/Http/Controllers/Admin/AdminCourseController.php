<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\CourseDetails;
use App\Models\CoursePhases;
use App\Models\CourseTopics;
use App\Models\CourseSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminCourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount(['contents', 'phases', 'schedules'])
            ->latest()
            ->get();
        return view('admin.pages.courses.view_course', compact('courses'));
    }

    public function create()
    {
        return view('admin.pages.courses.add_course');
    }

    public function store(Request $request)
    {
        // 1) Validation
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:courses,slug',
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:4096',
            'status'      => 'nullable|in:draft,published',

            // Details
            'details'                   => 'nullable|array',
            'details.*.type'            => 'required|string|in:heading,paragraph,image,features,list',
            'details.*.order'           => 'nullable|integer|min:1',
            'details.*.content'         => 'nullable',                 // string or array (list)
            'details.*.heading'         => 'nullable|string|max:255',  // for features title
            'details.*.description'     => 'nullable',                 // string OR array (legacy items)
            'details.*.items'           => 'nullable|array',           // NEW: features items[]
            'details.*.items.*'         => 'nullable|string',
            'details.*.file'            => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:51200',

            // Phases & topics
            'phases'                    => 'nullable|array',
            'phases.*.title'            => 'required_with:phases|string|max:255',
            'phases.*.description'      => 'nullable|string',
            'phases.*.duration'         => 'nullable|integer|min:0',
            'phases.*.topics'           => 'nullable|array',
            'phases.*.topics.*'         => 'nullable|string',

            // Schedules
            'schedules'                 => 'nullable|array',
            'schedules.*.title'         => 'nullable|string|max:255',
            'schedules.*.start_date'    => 'nullable|date',
            'schedules.*.end_date'      => 'nullable|date|after_or_equal:schedules.*.start_date',
            'schedules.*.location'      => 'nullable|string|max:255',
            'schedules.*.type'          => 'nullable|string|in:virtual,hybrid,physical',
            'schedules.*.price'         => 'nullable|numeric|min:0',
            'schedules.*.price_usd'     => 'nullable|numeric|min:0',
            'schedules.*.tag'           => 'nullable|string|in:free,paid,both',
            'schedules.*.description'   => 'nullable|string',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                // Slug
                $slug = $validated['slug'] ?? Str::slug($validated['title']);
                $base = $slug;
                $i = 1;
                while (Course::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . ($i++);
                }

                // Thumbnail
                $thumb = null;
                if ($request->hasFile('thumbnail')) {
                    $thumb = $request->file('thumbnail')->store('courses/thumbnails', 'public');
                }

                // Create course
                $course = Course::create([
                    'title'       => $validated['title'],
                    'slug'        => $slug,
                    'description' => $validated['description'] ?? null,
                    'thumbnail'   => $thumb,
                    'status'      => $validated['status'] ?? 'draft',
                ]);

                // 2) DETAILS
                $details = $request->input('details', []);
                foreach ($details as $idx => $d) {
                    if (empty($d['type'])) continue;

                    $type  = $d['type'];
                    $order = (int)($d['order'] ?? $idx + 1);

                    $content = null;
                    $image   = null;

                    switch ($type) {
                        case 'heading':
                        case 'paragraph':
                            $content = isset($d['content']) && is_string($d['content']) ? $d['content'] : null;
                            break;

                        case 'list':
                            // store as JSON array of strings
                            $items = $d['content'] ?? [];
                            if (is_string($items)) $items = [$items];
                            if (!is_array($items)) $items = [];
                            $items = array_values(array_filter(array_map('strval', $items), fn($v) => $v !== ''));
                            $content = $items ? json_encode($items, JSON_UNESCAPED_UNICODE) : null;
                            break;

                        case 'features':
                            // normalize new + legacy shapes into one object {heading, description, items[]}
                            $heading = isset($d['heading']) && is_string($d['heading']) ? trim($d['heading']) : null;

                            $descRaw = $d['description'] ?? null;
                            $descStr = is_string($descRaw) ? trim($descRaw) : null;

                            // NEW: items[]
                            $itemsRaw = $d['items'] ?? [];

                            // LEGACY: description as array => items
                            if (is_array($descRaw) && empty($itemsRaw)) {
                                $itemsRaw = $descRaw;
                                $descStr = null;
                            }

                            if (is_string($itemsRaw)) {
                                $itemsRaw = array_map('trim', explode(',', $itemsRaw));
                            }
                            $items = array_values(array_filter(array_map('strval', (array)$itemsRaw), fn($v) => $v !== ''));

                            $payload = [
                                'heading'     => $heading ?: null,
                                'description' => $descStr ?: null,
                                'items'       => $items,
                            ];

                            $content = json_encode($payload, JSON_UNESCAPED_UNICODE);
                            break;

                        case 'image':
                            $fileKey = "details.$idx.file";
                            if ($request->hasFile($fileKey) && $request->file($fileKey)->isValid()) {
                                $image = $request->file($fileKey)->store('courses/details/images', 'public');
                            }
                            $content = null;
                            break;
                    }

                    CourseDetails::create([
                        'course_id'  => $course->id,
                        'type'       => $type,
                        'sort_order' => $order,
                        'content'    => $content,
                        'image'      => $image,
                    ]);
                }

                // 3) PHASES + topics (titles only)
                $phases = $request->input('phases', []);
                foreach ($phases as $pIndex => $p) {
                    if (empty($p['title'])) continue;

                    $phase = CoursePhases::create([
                        'course_id' => $course->id,
                        'title'     => trim($p['title']),
                        'order'     => $pIndex + 1,
                        'duration'  => isset($p['duration']) ? (int)$p['duration'] : null,
                        'content'   => $p['description'] ?? null,
                    ]);

                    $topics = $p['topics'] ?? [];
                    foreach ($topics as $tIndex => $tTitle) {
                        if (!$tTitle) continue;
                        CourseTopics::create([
                            'course_phase_id' => $phase->id,
                            'title'   => trim((string)$tTitle),
                            'content' => null,
                            'order'   => $tIndex + 1,
                        ]);
                    }
                }

                // 4) SCHEDULES
                $schedules = $request->input('schedules', []);
                foreach ($schedules as $s) {
                    if (empty($s['start_date']) && empty($s['end_date'])) continue;

                    CourseSchedule::create([
                        'course_id'   => $course->id,
                        'title'       => $s['title'] ?? null,
                        'start_date'  => $s['start_date'] ?? null,
                        'end_date'    => $s['end_date'] ?? null,
                        'location'    => $s['location'] ?? null,
                        'type'        => $s['type'] ?? null,
                        'price'       => (isset($s['price']) && $s['price'] !== '') ? (float)$s['price'] : null,
                        'price_usd'   => (isset($s['price_usd']) && $s['price_usd'] !== '') ? (float)$s['price_usd'] : null,
                        'tag'         => $s['tag'] ?? null,
                        'description' => $s['description'] ?? null,
                    ]);
                }

                return redirect()->route('admin.courses.create', $course->slug)
                    ->with('success', 'Course created successfully.');
            });
        } catch (\Exception $e) {
            Log::error('Course creation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'stack'        => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'Course creation failed: ' . $e->getMessage()])->withInput();
        }
    }



    public function dashboard($id)
    {
        $course = Course::with([
            'details' => function ($q) {
                $q->reorder()->orderBy('sort_order'); // <- resets any previous "position" ordering
            },
            'schedules'
        ])->findOrFail($id);

        return view('admin.pages.courses.course_details', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:courses,slug,' . $id,
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:4096',
            'status' => 'nullable|in:draft,published',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course->update($validated);

        return back()->with('success', 'Course updated successfully!');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully!');
    }

    public function storeDetails(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'blocks'                 => 'required|array|min:1',
            'blocks.*.type'          => 'required|in:heading,paragraph,image,features,list',
            'blocks.*.content'       => 'nullable|string',
            'blocks.*.file_path'     => 'nullable|file|max:51200',
            'blocks.*.order'         => 'nullable|integer|min:1',

            // Features (your UI posts features[heading], features[description], features[items][])
            'blocks.*.features'                  => 'nullable|array',
            'blocks.*.features.heading'          => 'nullable|string|max:255',
            'blocks.*.features.description'      => 'nullable|string',
            'blocks.*.features.items'            => 'nullable|array',
            'blocks.*.features.items.*'          => 'nullable|string',

            // List
            'blocks.*.list'          => 'nullable|array',
            'blocks.*.list.*'        => 'nullable|string',
        ]);

        foreach ($validated['blocks'] as $index => $block) {
            $filePath = null;

            if (!empty($block['file_path'])) {
                $filePath = $block['file_path']->store('courses/contents', 'public');
            }

            $content = null;

            switch ($block['type']) {
                case 'heading':
                case 'paragraph':
                    $content = $block['content'] ?? null;
                    break;

                case 'features': {
                        // Read from features[...] group (matches your admin UI)
                        $heading = data_get($block, 'features.heading');
                        $descRaw = data_get($block, 'features.description');
                        $itemsRaw = data_get($block, 'features.items', []);

                        if (is_string($itemsRaw)) {
                            $itemsRaw = array_map('trim', explode(',', $itemsRaw));
                        }

                        $items = array_values(array_filter(
                            array_map('strval', (array) $itemsRaw),
                            fn($v) => $v !== ''
                        ));

                        $payload = [
                            'heading'     => ($heading !== null && $heading !== '') ? trim($heading) : null,
                            'description' => (is_string($descRaw) && $descRaw !== '') ? trim($descRaw) : null,
                            'items'       => $items,
                        ];

                        $content = json_encode($payload, JSON_UNESCAPED_UNICODE);
                        break;
                    }

                case 'list':
                    $list = $block['list'] ?? [];
                    $list = array_values(array_filter(
                        array_map(fn($v) => trim((string) $v), (array) $list),
                        fn($v) => $v !== ''
                    ));
                    $content = $list ? json_encode($list, JSON_UNESCAPED_UNICODE) : null;
                    break;

                case 'image':
                    $content = null; // image path saved below
                    break;
            }

            $nextOrder = $block['order'] ?? (($course->details()->max('sort_order') ?? 0) + 1);

            CourseDetails::create([
                'course_id'  => $course->id,
                'type'       => $block['type'],
                'content'    => $content,
                'image'      => $filePath,
                'sort_order' => $nextOrder,
            ]);
        }

        return back()->with('success', 'Course details added successfully!');
    }



    public function updateDetails(Request $request, $courseId, $contentId)
    {
        $content = CourseDetails::where('course_id', $courseId)->findOrFail($contentId);

        // Accept BOTH flat fields (heading, description, items[]) and nested features[...] from the edit modal
        $validated = $request->validate([
            'type'        => 'required|in:heading,paragraph,image,features,list',
            'content'     => 'nullable|string',
            'file_path'   => 'nullable|file|max:51200',
            'order'       => 'nullable|integer|min:1',

            // Flat (what your Blade currently sends)
            'heading'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'items'       => 'nullable|array',
            'items.*'     => 'nullable|string',

            // Nested (legacy / future-proof)
            'features'                 => 'nullable|array',
            'features.heading'         => 'nullable|string|max:255',
            'features.description'     => 'nullable|string',
            'features.items'           => 'nullable|array',
            'features.items.*'         => 'nullable|string',

            // List
            'list'        => 'nullable|array',
            'list.*'      => 'nullable|string',
        ]);

        // Image handling (keep old file unless a new one comes in)
        $filePath = $content->image;
        if ($request->hasFile('file_path')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file_path')->store('courses/contents', 'public');
        }

        // Prepare content by type
        $contentValue = null;

        switch ($validated['type']) {
            case 'heading':
            case 'paragraph':
                $contentValue = $validated['content'] ?? null;
                break;

            case 'features': {
                    // Prefer flat fields; fallback to nested features[...]
                    $heading = $request->input('heading', $request->input('features.heading'));
                    $descRaw = $request->input('description', $request->input('features.description'));

                    // Items may be flat items[] or nested features[items][]
                    $itemsRaw = $request->input('items', $request->input('features.items', []));

                    if (is_string($itemsRaw)) {
                        $itemsRaw = array_map('trim', explode(',', $itemsRaw));
                    }

                    $items = array_values(array_filter(
                        array_map('strval', (array) $itemsRaw),
                        fn($v) => trim($v) !== ''
                    ));

                    $payload = [
                        'heading'     => (is_string($heading) && trim($heading) !== '') ? trim($heading) : null,
                        'description' => (is_string($descRaw) && trim($descRaw) !== '') ? trim($descRaw) : null,
                        'items'       => $items,
                    ];

                    $contentValue = json_encode($payload, JSON_UNESCAPED_UNICODE);
                    break;
                }

            case 'list':
                $list = $validated['list'] ?? [];
                $list = array_values(array_filter(
                    array_map(static fn($v) => trim((string) $v), (array) $list),
                    static fn($v) => $v !== ''
                ));
                $contentValue = $list ? json_encode($list, JSON_UNESCAPED_UNICODE) : null;
                break;

            case 'image':
                $contentValue = null; // image path lives in $filePath
                break;
        }

        $content->update([
            'type'       => $validated['type'],
            'content'    => $contentValue,
            'image'      => $filePath,
            'sort_order' => $validated['order'] ?? $content->sort_order,
        ]);

        return back()->with('success', 'Course detail updated successfully!');
    }




    public function destroyDetails($courseId, $contentId)
    {
        $content = CourseDetails::where('course_id', $courseId)->findOrFail($contentId);

        if ($content->file_path) {
            Storage::disk('public')->delete($content->file_path);
        }

        $content->delete();

        return back()->with('success', 'Details deleted successfully!');
    }

    // Phase Management

    public function storePhase(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        try {
            // Log the full incoming request for debugging
            Log::info('storePhase Request data:', $request->all());

            // Validate phase fields
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'nullable|string',
                'duration' => 'nullable|integer|min:0',
                'image' => 'nullable|image|max:4096',
                'order' => 'nullable|integer|min:1',
                'topics' => 'nullable|array',
                'topics.*.title' => 'required|string|max:255',
                'topics.*.order' => 'nullable|integer|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            Log::error('storePhase Validation failed:', $e->errors());

            // Optionally: return back with the old input + errors
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Catch any other exception
            Log::error('storePhase Exception:', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong. Check logs.');
        }

        // Handle phase image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses/phases', 'public');
        }

        // Create the phase
        $phase = CoursePhases::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'image' => $imagePath,
            'order' => $validated['order'] ?? ($course->phases()->max('order') + 1),
        ]);

        // Create topics if any
        if (!empty($validated['topics'])) {
            foreach ($validated['topics'] as $topicData) {
                CourseTopics::create([
                    'course_phase_id' => $phase->id,
                    'title' => $topicData['title'],
                    'order' => $topicData['order'] ?? ($phase->topics()->max('order') + 1),
                ]);
            }
        }

        Log::info('Phase and topics created successfully', [
            'phase_id' => $phase->id,
            'topics' => $validated['topics'] ?? []
        ]);

        return back()->with('success', 'Phase and topics added successfully!');
    }



    public function updatePhase(Request $request, $courseId, $phaseId)
    {
        $phase = CoursePhases::where('course_id', $courseId)->findOrFail($phaseId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:4096',
            'order' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            if ($phase->image) {
                Storage::disk('public')->delete($phase->image);
            }
            $validated['image'] = $request->file('image')->store('courses/phases', 'public');
        }

        $phase->update($validated);

        return back()->with('success', 'Phase updated successfully!');
    }

    public function destroyPhase($courseId, $phaseId)
    {
        $phase = CoursePhases::where('course_id', $courseId)->findOrFail($phaseId);

        if ($phase->image) {
            Storage::disk('public')->delete($phase->image);
        }

        $phase->delete();

        return back()->with('success', 'Phase deleted successfully!');
    }

    // Topic Management
    public function storeTopic(Request $request, $courseId, $phaseId)
    {
        $phase = CoursePhases::where('course_id', $courseId)->findOrFail($phaseId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
        ]);

        CourseTopics::create([
            'course_phase_id' => $phase->id,
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'order' => $validated['order'] ?? ($phase->topics()->max('order') + 1),
        ]);

        return back()->with('success', 'Topic added successfully!');
    }



    // Schedule Management
    // Schedule Management
    public function storeSchedule(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'location'    => 'nullable|string|max:255',
            'type'        => 'nullable|string|in:virtual,hybrid,physical',
            'price'       => 'nullable|numeric|min:0',
            // NEW fields
            'price_usd'   => 'nullable|numeric|min:0',
            'tag'         => 'nullable|string|in:free,paid,both',
            'description' => 'nullable|string',
        ]);

        CourseSchedule::create([
            'course_id'   => $course->id,
            'title'       => $validated['title'] ?? null,
            'start_date'  => $validated['start_date'],
            'end_date'    => $validated['end_date'],
            'location'    => $validated['location'] ?? null,
            'type'        => $validated['type'] ?? null,
            'price'       => array_key_exists('price', $validated) ? (float)$validated['price'] : null,
            'price_usd'   => array_key_exists('price_usd', $validated) ? (float)$validated['price_usd'] : null,
            'tag'         => $validated['tag'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Schedule added successfully!');
    }


    public function updateSchedule(Request $request, $courseId, $scheduleId)
    {
        $schedule = CourseSchedule::where('course_id', $courseId)->findOrFail($scheduleId);

        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'location'    => 'nullable|string|max:255',
            'type'        => 'nullable|string|in:virtual,hybrid,physical',
            'price'       => 'nullable|numeric|min:0',
            // NEW
            'price_usd'   => 'nullable|numeric|min:0',
            'tag'         => 'nullable|string|in:free,paid,both',
            'description' => 'nullable|string',
        ]);

        // Ensure proper nulling of optional numerics if the field is omitted
        $schedule->update([
            'title'       => $validated['title'] ?? $schedule->title,
            'start_date'  => $validated['start_date'],
            'end_date'    => $validated['end_date'],
            'location'    => $validated['location'] ?? null,
            'type'        => $validated['type'] ?? null,
            'price'       => array_key_exists('price', $validated) ? (float)$validated['price'] : null,
            'price_usd'   => array_key_exists('price_usd', $validated) ? (float)$validated['price_usd'] : null,
            'tag'         => $validated['tag'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Schedule updated successfully!');
    }


    public function destroySchedule($courseId, $scheduleId)
    {
        $schedule = CourseSchedule::where('course_id', $courseId)->findOrFail($scheduleId);

        $schedule->delete();

        return back()->with('success', 'Schedule deleted successfully!');
    }
    public function updateTopic($courseId, $phaseId, $topicId, Request $request)
    {

        $topic = CourseTopics::where('course_phase_id', $phaseId)->findOrFail($topicId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
        ]);

        $topic->update($validated);

        return back()->with('success', 'Topic updated successfully!');
    }

    public function destroyTopic($courseId, $phaseId, $topicId)
    {
        $topic = CourseTopics::where('course_id', $courseId)->findOrFail($topicId);
        $topic->delete();

        return back()->with('success', 'Topic deleted successfully!');
    }

    public function courseContent()
    {
        $contents = CourseContent::with('course')->latest()->get();
        $courses = Course::all();
        return view('admin.pages.courses.contents', compact('contents', 'courses'));
    }

    public function storeContent(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:text,video,pdf,image,quiz,assignment',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price'
        ]);


        $filePath = null;

        if ($request->hasFile('file')) {
            $allowedMimes = [
                'video' => ['mp4', 'avi', 'mov', 'wmv'],
                'pdf' => ['pdf'],
                'image' => ['jpg', 'jpeg', 'png', 'webp'],
                'quiz' => ['pdf', 'doc', 'docx'],
                'assignment' => ['pdf', 'doc', 'docx'],
            ];

            $ext = $request->file('file')->getClientOriginalExtension();
            if (in_array($ext, $allowedMimes[$request->type] ?? [])) {
                $filePath = $request->file('file')->store("courses/contents/{$request->type}", 'public');
            }
        }

        CourseContent::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'type' => $request->type,
            'content' => $request->content,
            'file_path' => $filePath,
            'order' => CourseContent::where('course_id', $request->course_id)->count() + 1,
        ]);

        // ✅ Update course price & discount if provided
        if ($request->filled('price') || $request->filled('discount_price')) {
            $course = Course::find($request->course_id);
            if ($request->filled('price')) {
                $course->price = $request->price;
            }
            if ($request->filled('discount_price')) {
                $course->discount_price = $request->discount_price;
            }
            $course->save();
        }


        return redirect()->back()->with('success', 'Course content added successfully!');
    }
    public function updateContent(Request $request, CourseContent $courseContent)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        // delete old file
        if ($courseContent->file_path && Storage::disk('public')->exists($courseContent->file_path)) {
            Storage::disk('public')->delete($courseContent->file_path);
        }

        $ext = $request->file('file')->getClientOriginalExtension();
        $type = $courseContent->type;

        $allowedMimes = [
            'video' => ['mp4', 'avi', 'mov', 'wmv'],
            'pdf' => ['pdf'],
            'image' => ['jpg', 'jpeg', 'png', 'webp'],
            'quiz' => ['pdf', 'doc', 'docx'],
            'assignment' => ['pdf', 'doc', 'docx'],
        ];

        if (!in_array($ext, $allowedMimes[$type] ?? [])) {
            return back()->with('error', 'Invalid file type for this content.');
        }

        $filePath = $request->file('file')->store("courses/contents/{$type}", 'public');
        $courseContent->update(['file_path' => $filePath]);

        return back()->with('success', 'File updated successfully!');
    }

    public function destroyContent(CourseContent $courseContent)
    {
        if ($courseContent->file_path && Storage::disk('public')->exists($courseContent->file_path)) {
            Storage::disk('public')->delete($courseContent->file_path);
        }
        $courseContent->delete();

        return redirect()->back()->with('success', 'Course content deleted successfully!');
    }

    public function showContent($courseId)
    {
        $course = Course::with('contents')->findOrFail($courseId);
        return view('admin.pages.courses.course_contents', compact('course'));
    }
}
