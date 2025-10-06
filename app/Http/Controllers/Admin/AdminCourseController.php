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

            // contents validation
            'details'                  => 'nullable|array',
            'details.*.type'           => 'required_with:contents|string|in:text,video,pdf,quiz,assignment',
            'contents.*.content'        => 'nullable|string',
            'contents.*.order'          => 'nullable|integer|min:1',

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
            'schedules.*.type'          => 'nullable|string|max:100',
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
                        'image'      => null,
                        'content'    => $detailData['content'] ?? null,
                    ];

                    // Handle file uploads for image type
                    $fileKey = "details.{$index}.file";
                    if ($type === 'image' && $request->hasFile($fileKey)) {
                        $file = $request->file($fileKey);
                        if ($file->isValid()) {
                            $data['image'] = $file->store("courses/details/images", 'public');
                        }
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

                return redirect()->route('courses.create', $course->slug)
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
        $course = CourseDetails::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:text,video,pdf,quiz,assignment',
            'content' => 'nullable|string',
            'file_path' => 'nullable|file|max:51200', // 50MB
            'order' => 'nullable|integer|min:1',
        ]);

        $filePath = null;
        if ($request->hasFile('file_path')) {
            $filePath = $request->file('file_path')->store('courses/contents', 'public');
        }

        CourseDetails::create([
            'course_id' => $course->id,
            'type' => $validated['type'],
            'content' => $validated['content'] ?? null,
            'sort_order' => $filePath,
            'image' => $validated['order'] ?? ($course->contents()->max('order') + 1),
        ]);

        return back()->with('success', 'Details added successfully!');
    }

    public function updateDetails(Request $request, $courseId, $contentId)
    {
        $content = CourseDetails::where('course_id', $courseId)->findOrFail($contentId);

        $validated = $request->validate([
            'content' => 'nullable|string',
            'image' => 'nullable|file|max:51200',
            'sort_order' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('file_path')) {
            if ($content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }
            $validated['file_path'] = $request->file('file_path')->store('courses/contents', 'public');
        }

        $content->update($validated);

        return back()->with('success', 'Details updated successfully!');
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

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:4096',
            'order' => 'nullable|integer|min:1',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses/phases', 'public');
        }

        CoursePhases::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'image' => $imagePath,
            'order' => $validated['order'] ?? ($course->phases()->max('order') + 1),
        ]);

        return back()->with('success', 'Phase added successfully!');
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