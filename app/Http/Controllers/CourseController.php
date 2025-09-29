<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\CourseContent;
use App\Models\CoursePhases;
use App\Models\CourseTopics;
use App\Models\CourseSchedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function getCourse()
    {
        $course = Course::where('status', 'published')
            ->latest()
            ->paginate(10);

        return view('user.pages.academy', compact('course'));
    }

    public function showdetails($slug)
    {
        $course = Course::with([
            'phases.topics',
            'schedules',
        ])->where('slug', $slug)->firstOrFail();

        return view('user.pages.course_details', compact('course'));
    }

    public function shop(Request $request)
    {
        $query = Course::where('status', 'published');

        if ($request->orderby == 'title') {
            $query->orderBy('title', 'asc');
        } else {
            $query->latest();
        }

        $course = $query->paginate(6);
        $latestCourse = Course::where('status', 'published')->latest()->take(3)->get();

        return view('user.pages.shop', compact('course', 'latestCourse'));
    }

    public function shopDetails($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        return view('user.pages.shop_details', compact('course'));
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
            'contents'                  => 'nullable|array',
            'contents.*.title'          => 'required_with:contents|string|max:255',
            'contents.*.type'           => 'required_with:contents|string|in:text,video,pdf,quiz,assignment',
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

                // Handle Course Contents
                $contents = $request->input('contents', []);
                foreach ($contents as $index => $contentData) {
                    if (empty($contentData['title'])) {
                        continue; // Skip empty content blocks
                    }

                    $type = $contentData['type'] ?? 'text';
                    $title = trim($contentData['title']);
                    $order = (int) ($contentData['order'] ?? $index + 1);

                    $data = [
                        'course_id' => $course->id,
                        'title'     => $title,
                        'type'      => $type,
                        'order'     => $order,
                        'file_path' => null,
                        'content'   => null,
                    ];

                    // Handle file uploads for non-text content
                    if (in_array($type, ['video', 'pdf', 'quiz', 'assignment'])) {
                        // Check if file was uploaded for this content block
                        $fileKey = "contents.{$index}.file";
                        if ($request->hasFile($fileKey)) {
                            $file = $request->file($fileKey);
                            $allowedMimes = [
                                'video' => ['mp4', 'avi', 'mov', 'wmv'],
                                'pdf' => ['pdf'],
                                'quiz' => ['pdf', 'doc', 'docx'],
                                'assignment' => ['pdf', 'doc', 'docx']
                            ];

                            $extension = $file->getClientOriginalExtension();
                            if (in_array($extension, $allowedMimes[$type] ?? [])) {
                                $data['file_path'] = $file->store("courses/contents/{$type}", 'public');
                            }
                        }
                        // Store any additional notes/description
                        $data['content'] = $contentData['content'] ?? null;
                    } else {
                        // For text content
                        $data['content'] = $contentData['content'] ?? null;
                    }

                    CourseContent::create($data);
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
}
