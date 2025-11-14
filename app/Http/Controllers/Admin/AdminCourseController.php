<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseContent;
use App\Models\CourseDetails;
use App\Models\CourseFaq;
use App\Models\CoursePhases;
use App\Models\CourseTopics;
use App\Models\CourseSchedule;
use App\Models\CourseTestimonials;
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

            // Single schedule payload
            'schedule'              => 'nullable|array',
            'schedule.start_date'   => 'nullable|date',
            'schedule.end_date'     => 'nullable|date|after_or_equal:schedule.start_date',
            'schedule.location'     => 'nullable|string|max:255',
            'schedule.type'         => 'nullable|string|in:virtual,hybrid,physical',
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

                // Schedule (single)
                $scheduleData = $validated['schedule'] ?? null;
                if (is_array($scheduleData)) {
                    $hasValue = collect($scheduleData)->filter(fn($value) => !is_null($value) && $value !== '')->isNotEmpty();

                    if ($hasValue) {
                        CourseSchedule::create([
                            'course_id'  => $course->id,
                            'start_date' => $scheduleData['start_date'] ?? null,
                            'end_date'   => $scheduleData['end_date'] ?? null,
                            'location'   => $scheduleData['location'] ?? null,
                            'type'       => $scheduleData['type'] ?? null,
                        ]);
                    }
                }

                return redirect()->route('admin.courses.create')
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



    // Phase Management

    /** ===================== PHASES ===================== */

    public function storePhase(Request $request, CourseContent $content)
    {
        try {
            Log::info('storePhase payload', $request->all());

            $validated = $request->validate([
                'title'                 => 'required|string|max:255',
                'content'               => 'nullable|string',
                'duration'              => 'nullable|integer|min:0',
                'image'                 => 'nullable|image|max:4096',
                'order'                 => 'nullable|integer|min:1',
                'topics'                => 'nullable|array',
                'topics.*.title'        => 'required|string|max:255',
                'topics.*.content'      => 'nullable|string',
                'topics.*.order'        => 'nullable|integer|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('storePhase validation', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses/phases', 'public');
        }

        $phase = CoursePhases::create([
            'course_content_id' => $content->id,
            'title'             => $validated['title'],
            'content'           => $validated['content'] ?? null,
            'duration'          => $validated['duration'] ?? null,
            'image'             => $imagePath,
            'order'             => $validated['order'] ?? (($content->phases()->max('order') ?? 0) + 1),
        ]);

        // optional topics
        if (!empty($validated['topics'])) {
            $nextOrder = ($phase->topics()->max('order') ?? 0);
            foreach ($validated['topics'] as $t) {
                $nextOrder = $t['order'] ?? ($nextOrder + 1);
                CourseTopics::create([
                    'course_phase_id' => $phase->id,
                    'title'           => $t['title'],
                    'content'         => $t['content'] ?? null,
                    'order'           => $nextOrder,
                ]);
            }
        }

        Log::info('storePhase ok', ['phase_id' => $phase->id]);
        return back()->with('success', 'Phase and topics added successfully!');
    }

    public function updatePhase(Request $request, CourseContent $content, CoursePhases $phase)
    {
        // With ->scopeBindings(), $phase already belongs to $content or 404.
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'image'    => 'nullable|image|max:4096',
            'order'    => 'nullable|integer|min:1',
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

    public function destroyPhase(CourseContent $content, CoursePhases $phase)
    {
        if ($phase->image) {
            Storage::disk('public')->delete($phase->image);
        }

        // Topics should cascade via FK. If not, uncomment the manual delete:
        // $phase->topics()->delete();

        $phase->delete();

        return back()->with('success', 'Phase deleted successfully!');
    }


    /** ===================== TOPICS ===================== */

    public function storeTopic(Request $request, CourseContent $content, CoursePhases $phase)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'order'   => 'nullable|integer|min:1',
        ]);

        CourseTopics::create([
            'course_phase_id' => $phase->id,
            'title'           => $validated['title'],
            'content'         => $validated['content'] ?? null,
            'order'           => $validated['order'] ?? (($phase->topics()->max('order') ?? 0) + 1),
        ]);

        return back()->with('success', 'Topic added successfully!');
    }

    public function updateTopic(Request $request, CourseContent $content, CoursePhases $phase, CourseTopics $topic)
    {
        // With ->scopeBindings(), $topic belongs to $phase which belongs to $content or 404.
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'order'   => 'nullable|integer|min:1',
        ]);

        $topic->update($validated);

        return back()->with('success', 'Topic updated successfully!');
    }

    public function destroyTopic(CourseContent $content, CoursePhases $phase, CourseTopics $topic)
    {
        $topic->delete();
        return back()->with('success', 'Topic deleted successfully!');
    }

    // Schedule Management
    // Schedule Management
    public function storeSchedule(Request $request, $id)
    {
        $course = Course::withCount('schedules')->findOrFail($id);

        if ($course->schedules_count > 0) {
            return back()->withErrors([
                'schedule' => 'This course already has a schedule. Edit the existing one instead of creating another.',
            ]);
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'location'   => 'nullable|string|max:255',
            'type'       => 'nullable|string|in:virtual,hybrid,physical',
        ]);

        $resolvedType = $validated['type'] ?? 'bootcamp';

        CourseSchedule::create([
            'course_id'  => $course->id,
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'] ?? null,
            'location'   => $validated['location'] ?? null,
            'type'       => $resolvedType,
        ]);

        return back()->with('success', 'Schedule added successfully!');
    }


    public function updateSchedule(Request $request, $courseId, $scheduleId)
    {
        $schedule = CourseSchedule::where('course_id', $courseId)->findOrFail($scheduleId);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'location'   => 'nullable|string|max:255',
            'type'       => 'nullable|string|in:virtual,hybrid,physical',
        ]);

        $resolvedType = $validated['type'] ?? $schedule->type ?? 'bootcamp';

        $schedule->update([
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'] ?? null,
            'location'   => $validated['location'] ?? null,
            'type'       => $resolvedType,
        ]);

        return back()->with('success', 'Schedule updated successfully!');
    }


    public function destroySchedule($courseId, $scheduleId)
    {
        $schedule = CourseSchedule::where('course_id', $courseId)->findOrFail($scheduleId);

        $schedule->delete();

        return back()->with('success', 'Schedule deleted successfully!');
    }


    public function courseContent()
    {
        $courses = Course::with(['contents' => function ($query) {
            $query->withCount('phases')->orderBy('order')->orderBy('created_at');
        }])->withCount('contents')->latest()->get();

        $allCourses = Course::orderBy('title')->get(['id', 'title']);

        return view('admin.pages.courses.contents', [
            'courses' => $courses,
            'courseOptions' => $allCourses,
        ]);
    }

    public function storeContent(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:text,video,pdf,image,quiz,assignment',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'phases' => 'nullable|array',
            'phases.*.title' => 'required_with:phases|string|max:255',
            'phases.*.order' => 'nullable|integer|min:1',
            'phases.*.duration' => 'nullable|integer|min:0',
            'phases.*.content' => 'nullable|string',
            'phases.*.image' => 'nullable|image|max:4096',
            'phases.*.topics' => 'nullable|array',
            'phases.*.topics.*.title' => 'required_with:phases.*.topics|string|max:255',
            'phases.*.topics.*.order' => 'nullable|integer|min:1',
            'phases.*.topics.*.content' => 'nullable|string',
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

        $nextOrder = (int) CourseContent::where('course_id', $validated['course_id'])->max('order') + 1;

        $content = CourseContent::create([
            'course_id' => $validated['course_id'],
            'title' => $validated['title'],
            'type' => $validated['type'],
            'content' => $validated['type'] === 'text' ? $validated['content'] : null,
            'file_path' => $filePath,
            'order' => $nextOrder,
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

        $phases = $request->input('phases', []);
        foreach ($phases as $index => $phaseData) {
            if (empty($phaseData['title'])) {
                continue;
            }

            $phaseImage = $request->file("phases.$index.image");
            $phaseImagePath = $phaseImage ? $phaseImage->store('courses/phases', 'public') : null;

            $phase = CoursePhases::create([
                'course_content_id' => $content->id,
                'title' => $phaseData['title'],
                'order' => $phaseData['order'] ?? ($index + 1),
                'duration' => $phaseData['duration'] ?? null,
                'content' => $phaseData['content'] ?? null,
                'image' => $phaseImagePath,
            ]);

            $topics = $phaseData['topics'] ?? [];
            foreach ($topics as $topicIndex => $topic) {
                if (empty($topic['title'])) {
                    continue;
                }

                CourseTopics::create([
                    'course_phase_id' => $phase->id,
                    'title' => $topic['title'],
                    'content' => $topic['content'] ?? null,
                    'order' => $topic['order'] ?? ($topicIndex + 1),
                ]);
            }
        }


        return redirect()->back()->with('success', 'Course content added successfully!');
    }
    public function updateContent(Request $request, CourseContent $courseContent)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type'  => 'required|in:text,video,pdf,image,quiz,assignment',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $allowedMimes = [
            'video' => ['mp4', 'avi', 'mov', 'wmv'],
            'pdf' => ['pdf'],
            'image' => ['jpg', 'jpeg', 'png', 'webp'],
            'quiz' => ['pdf', 'doc', 'docx'],
            'assignment' => ['pdf', 'doc', 'docx'],
        ];

        if ($data['type'] === 'text') {
            $data['file_path'] = null;
        }

        // delete old file
        $typeChanged = $data['type'] !== $courseContent->type;

        if ($data['type'] === 'text' && $courseContent->file_path && Storage::disk('public')->exists($courseContent->file_path)) {
            Storage::disk('public')->delete($courseContent->file_path);
            $courseContent->file_path = null;
        }

        if ($data['type'] !== 'text' && $typeChanged && !$request->hasFile('file') && !$courseContent->file_path) {
            return back()->with('error', 'Please upload a file for the selected content type.');
        }

        if ($request->hasFile('file')) {
            $ext = $request->file('file')->getClientOriginalExtension();
            $type = $data['type'];

            if (!in_array($ext, $allowedMimes[$type] ?? [])) {
                return back()->with('error', 'Invalid file type for this content.');
            }

            if ($courseContent->file_path && Storage::disk('public')->exists($courseContent->file_path)) {
                Storage::disk('public')->delete($courseContent->file_path);
            }

            $data['file_path'] = $request->file('file')->store("courses/contents/{$type}", 'public');
        } elseif ($data['type'] !== 'text' && empty($courseContent->file_path)) {
            return back()->with('error', 'Please upload a file for this content type.');
        }

        $courseContent->update([
            'title' => $data['title'],
            'type' => $data['type'],
            'content' => $data['type'] === 'text' ? $data['content'] : null,
            'file_path' => $data['type'] === 'text' ? null : ($data['file_path'] ?? $courseContent->file_path),
        ]);

        return back()->with('success', 'Content updated successfully!');
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
        $course = Course::with(['contents' => function ($query) {
            $query->orderBy('order')->orderBy('created_at');
        }, 'contents.phases.topics'])->findOrFail($courseId);

        $courseOptions = Course::orderBy('title')->get(['id', 'title']);

        return view('admin.pages.courses.course_contents', compact('course', 'courseOptions'));
    }

    // TESTIMONIALS
    public function testimonialsIndex(Course $course)
    {
        $items   = CourseTestimonials::latest()->paginate(20);
        $courses = Course::orderBy('title')->get(['id', 'title']); // for the Add modal select
        return view('admin.pages.courses.testimonials', compact('course', 'items', 'courses'));
    }

    public function testimonialsStore(Request $r) // global store (course selected in form)
    {
        $data = $r->validate([
            'course_id'    => ['required', 'exists:courses,id'],
            'name'         => ['required', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:160'],
            'body'         => ['required', 'string', 'max:2000'],
            'image'        => ['nullable', 'image', 'max:4096'],
        ]);

        if ($r->hasFile('image')) {
            $data['image'] = $r->file('image')->store('testimonials', 'public');
        }

        $course = Course::findOrFail($data['course_id']);
        $course->testimonials()->create($data);

        return redirect()
            ->route('admin.courses.testimonials.index', $course->id)
            ->with('success', 'Testimonial created');
    }

    public function testimonialsUpdate(Request $r, Course $course, CourseTestimonials $testimonial)
    {
        abort_if($testimonial->course_id !== $course->id, 404);

        $data = $r->validate([
            'name'         => ['required', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:160'],
            'body'         => ['required', 'string', 'max:2000'],
            'image'        => ['nullable', 'image', 'max:4096'],
        ]);

        if ($r->hasFile('image')) {
            if ($testimonial->image) Storage::disk('public')->delete($testimonial->image);
            $data['image'] = $r->file('image')->store('testimonials', 'public');
        }

        $testimonial->update($data);

        return redirect()
            ->route('admin.courses.testimonials.index', $course->id)
            ->with('success', 'Testimonial updated');
    }

    public function testimonialsDestroy(Course $course, CourseTestimonials $testimonial)
    {
        abort_if($testimonial->course_id !== $course->id, 404);

        if ($testimonial->image) Storage::disk('public')->delete($testimonial->image);
        $testimonial->delete();

        return back()->with('success', 'Deleted');
    }

    public function faqsIndex(Course $course)
    {
        $items   = CourseFaq::orderByDesc('id')->paginate(30);
        $courses = Course::orderBy('title')->get(['id', 'title']); // Add modal select
        return view('admin.pages.courses.faq', compact('course', 'items', 'courses'));
    }

    public function faqsStore(Request $r) // global store
    {
        $data = $r->validate([
            'course_id'  => ['required', 'exists:courses,id'],
            'question'   => ['required', 'string', 'max:255'],
            'answer'     => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'between:0,65535'],
        ]);

        $course = Course::findOrFail($data['course_id']);
        $course->faqs()->create($data);

        return redirect()
            ->route('admin.courses.faqs.index', $course->id)
            ->with('success', 'FAQ created');
    }

    public function faqsUpdate(Request $r, Course $course, CourseFaq $faq)
    {
        abort_if($faq->course_id !== $course->id, 404);

        $data = $r->validate([
            'question'   => ['required', 'string', 'max:255'],
            'answer'     => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'between:0,65535'],
        ]);

        $faq->update($data);

        return redirect()
            ->route('admin.courses.faqs.index', $course->id)
            ->with('success', 'FAQ updated');
    }

    public function faqsDestroy(Course $course, CourseFaq $faq)
    {
        abort_if($faq->course_id !== $course->id, 404);
        $faq->delete();

        return back()->with('success', 'Deleted');
    }
}
