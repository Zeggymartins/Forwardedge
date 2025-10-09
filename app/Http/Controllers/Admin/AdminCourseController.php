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
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'slug'              => 'nullable|string|max:255|unique:courses,slug',
            'description'       => 'nullable|string',
            'thumbnail'         => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:4096',
            'status'            => 'nullable|in:draft,published',

            'details' => 'nullable|array',
            'details.*.type' => 'required|string|in:heading,paragraph,image,features,list',
            'details.*.content' => 'nullable|string',
            'details.*.heading' => 'nullable|string|max:255',
            'details.*.description' => 'nullable|string',

            // phases and topics
            'phases'                    => 'nullable|array',
            'phases.*.title'            => 'required_with:phases|string|max:255',
            'phases.*.description'      => 'nullable|string',
            'phases.*.duration'         => 'nullable|integer|min:0',
            'phases.*.topics'           => 'nullable|array',
            'phases.*.topics.*.title'   => 'required_with:phases.*.topics|string|max:255',
            'phases.*.topics.*.content' => 'nullable|string',

            // schedules
            'schedules'                 => 'nullable|array',
            'schedules.*.start_date'    => 'nullable|date',
            'schedules.*.end_date'      => 'nullable|date|after_or_equal:schedules.*.start_date',
            'schedules.*.location'      => 'nullable|string|max:255',
            'schedules.*.type'       => 'nullable|string|in:virtual,hybrid,physical',
            'schedules.*.price'         => 'nullable|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                // Generate unique slug
                $slug = $validated['slug'] ?? Str::slug($validated['title']);
                $originalSlug = $slug;
                $counter = 1;

                while (Course::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter++;
                }

                // Handle thumbnail upload
                $thumbnailPath = null;
                if ($request->hasFile('thumbnail')) {
                    $thumbnailPath = $request->file('thumbnail')->store('courses/thumbnails', 'public');
                }

                // Create course
                $course = Course::create([
                    'title' => $validated['title'],
                    'slug'  => $slug,
                    'description' => $validated['description'] ?? null,
                    'thumbnail' => $thumbnailPath,
                    'status' => $validated['status'] ?? 'draft',
                ]);

                $details = $request->input('details', []);
                foreach ($details as $index => $detailData) {
                    if (empty($detailData['type'])) continue;

                    $type  = $detailData['type'];
                    $order = (int) ($detailData['order'] ?? $index + 1);

                    $data = [
                        'course_id'  => $course->id,
                        'type'       => $type,
                        'sort_order' => $order,
                        'content'    => null,
                        'heading'    => null,
                        'description' => null,
                        'image'      => null,
                    ];

                    if ($type === 'features') {
                        $data['heading'] = $detailData['heading'] ?? null;
                        $data['description'] = $detailData['description'] ?? null;
                    } elseif ($type === 'image') {
                        $fileKey = "details.{$index}.file";
                        if ($request->hasFile($fileKey)) {
                            $file = $request->file($fileKey);
                            if ($file->isValid()) {
                                $data['image'] = $file->store("courses/details/images", 'public');
                            }
                        }
                    } else {
                        $data['content'] = $detailData['content'] ?? null;
                    }

                    CourseDetails::create($data);
                }


                // Handle Phases and Topics
                $phases = $request->input('phases', []);
                foreach ($phases as $phaseIndex => $phaseData) {
                    if (empty($phaseData['title'])) {
                        continue; // Skip empty phases
                    }

                    $phaseCreateData = [
                        'course_id' => $course->id,
                        'title'     => trim($phaseData['title']),
                        'order'     => $phaseIndex + 1,
                        'duration'  => !empty($phaseData['duration']) ? (int) $phaseData['duration'] : null,
                        'content'   => $phaseData['description'] ?? null,
                    ];

                    // Handle phase image upload
                    $phaseImageKey = "phases.{$phaseIndex}.image";
                    if ($request->hasFile($phaseImageKey)) {
                        $phaseImage = $request->file($phaseImageKey);
                        if ($phaseImage->isValid()) {
                            $phaseCreateData['image'] = $phaseImage->store('courses/phases', 'public');
                        }
                    }

                    // Only include image in create data if we have one or if the column allows NULL
                    // Check your migration - if image column is NOT NULL, you need to provide a default or make it nullable

                    $phase = CoursePhases::create($phaseCreateData);

                    // Handle Topics for this phase
                    $topics = $phaseData['topics'] ?? [];
                    foreach ($topics as $topicIndex => $topicData) {
                        if (empty($topicData['title'])) {
                            continue; // Skip empty topics
                        }

                        CourseTopics::create([
                            'course_phase_id' => $phase->id,
                            'title' => trim($topicData['title']),
                            'content' => $topicData['content'] ?? null,
                            'order' => $topicIndex + 1,
                        ]);
                    }
                }

                // Handle Schedules
                $schedules = $request->input('schedules', []);
                foreach ($schedules as $scheduleData) {
                    // Skip if both dates are empty
                    if (empty($scheduleData['start_date']) && empty($scheduleData['end_date'])) {
                        continue;
                    }

                    CourseSchedule::create([
                        'course_id' => $course->id,
                        'start_date' => $scheduleData['start_date'] ?? null,
                        'end_date' => $scheduleData['end_date'] ?? null,
                        'location' => $scheduleData['location'] ?? null,
                        'type' => $scheduleData['type'] ?? 'bootcamp',
                        'price' => !empty($scheduleData['price']) ? (float) $scheduleData['price'] : null,
                    ]);
                }

                return redirect()->route('admin.courses.create', $course->slug)
                    ->with('success', 'Course created successfully.');
            });
        } catch (\Exception $e) {
            Log::error('Course creation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Course creation failed: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function dashboard($id)
    {
        $course = Course::with([
            'details' => function($q) { $q->orderBy('order'); },
            'phases.topics',
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

    // Content Management
    public function storeDetails(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'blocks' => 'required|array|min:1',
            'blocks.*.type' => 'required|in:heading,paragraph,image,features,list',
            'blocks.*.content' => 'nullable|string',
            'blocks.*.file_path' => 'nullable|file|max:51200',
            'blocks.*.order' => 'nullable|integer|min:1',
            'blocks.*.features' => 'nullable|array',
            'blocks.*.features.*.heading' => 'nullable|string',
            'blocks.*.features.*.description' => 'nullable|string',
            'blocks.*.list' => 'nullable|array',
            'blocks.*.list.*' => 'nullable|string',
        ]);

        foreach ($validated['blocks'] as $index => $block) {
            $filePath = null;

            // Handle file upload if present
            if (isset($block['file_path'])) {
                $filePath = $block['file_path']->store('courses/contents', 'public');
            }

            // Determine content structure based on type
            $content = null;

            switch ($block['type']) {
                case 'heading':
                case 'paragraph':
                    $content = $block['content'] ?? null;
                    break;

                case 'features':
                    // Multiple features: heading + description
                    $content = isset($block['features'])
                        ? json_encode($block['features'])
                        : null;
                    break;

                case 'list':
                    // Multiple list items
                    $content = isset($block['list'])
                        ? json_encode($block['list'])
                        : null;
                    break;

                case 'image':
                    // Just reference uploaded file path
                    $content = null;
                    break;
            }

            CourseDetails::create([
                'course_id' => $course->id,
                'type' => $block['type'],
                'content' => $content,
                'image' => $filePath,
                'sort_order' => $block['order'] ?? ($course->details()->max('sort_order') + 1),
            ]);
        }

        return back()->with('success', 'Course details added successfully!');
    }


    public function updateDetails(Request $request, $courseId, $contentId)
    {
        $content = CourseDetails::where('course_id', $courseId)->findOrFail($contentId);

        $validated = $request->validate([
            'type' => 'required|in:heading,paragraph,image,features,list',
            'content' => 'nullable|string',
            'file_path' => 'nullable|file|max:51200',
            'features' => 'nullable|array',
            'features.*.heading' => 'nullable|string',
            'features.*.description' => 'nullable|string',
            'list' => 'nullable|array',
            'list.*' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
        ]);

        $filePath = $content->image;

        if ($request->hasFile('file_path')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file_path')->store('courses/contents', 'public');
        }

        // Prepare content based on type
        $contentValue = null;

        switch ($validated['type']) {
            case 'heading':
            case 'paragraph':
                $contentValue = $validated['content'] ?? null;
                break;

            case 'features':
                $contentValue = isset($validated['features'])
                    ? json_encode($validated['features'])
                    : null;
                break;

            case 'list':
                $contentValue = isset($validated['list'])
                    ? json_encode($validated['list'])
                    : null;
                break;

            case 'image':
                $contentValue = null;
                break;
        }

        $content->update([
            'type' => $validated['type'],
            'content' => $contentValue,
            'image' => $filePath,
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
    public function storeSchedule(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
        ]);

        CourseSchedule::create([
            'course_id' => $course->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'location' => $validated['location'] ?? null,
            'type' => $validated['type'] ?? 'bootcamp',
            'price' => $validated['price'] ?? null,
        ]);

        return back()->with('success', 'Schedule added successfully!');
    }

    public function updateSchedule(Request $request, $courseId, $scheduleId)
    {
        $schedule = CourseSchedule::where('course_id', $courseId)->findOrFail($scheduleId);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
        ]);

        $schedule->update($validated);

        return back()->with('success', 'Schedule updated successfully!');
    }

    public function destroySchedule($courseId, $scheduleId)
    {
        $schedule = CourseSchedule::where('course_id', $courseId)->findOrFail($scheduleId);

        $schedule->delete();

        return back()->with('success', 'Schedule deleted successfully!');
    }
    public function updateTopic($courseId, $phaseId, $topicId, Request $request){

    $topic =CourseTopics::where('course_phase_id', $phaseId)->findOrFail($topicId);

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