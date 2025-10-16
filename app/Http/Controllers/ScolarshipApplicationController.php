<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ScolarshipApplicationController extends Controller
{
    // ---------- Public application ----------
    public function Register(CourseSchedule $schedule)
    {
        $course = $schedule->course;
        abort_unless($schedule->isFree(), 404);
        return view('user.pages.scholarshipregistration', compact('schedule', 'course'));
    }

    public function storeData(Request $request, CourseSchedule $schedule)
    {
        abort_unless($schedule->isFree(), 404);

        $data = $request->validate([
            'why_join'   => 'required|string|max:2000',
            'experience' => 'nullable|string|max:2000',
            'commitment' => 'required|boolean',
        ]);

        ScholarshipApplication::create([
            'course_id'          => $schedule->course_id,
            'course_schedule_id' => $schedule->id,
            'user_id'            => $request->user()->id,
            'status'             => 'pending',
            'form_data'          => $data,
        ]);

        return redirect()
            ->route('course.show', $schedule->course->slug)
            ->with('success', 'Application submitted! We’ll notify you by email.');
    }

    // ---------- Admin: List ----------
    public function index()
    {
        $items = Scholarship::with('course')->latest()->get();
        return view('admin.pages.courses.scholarship.view', compact('items'));
    }

    // ---------- Admin: Create ----------
    public function create()
    {
        $courses = Course::orderBy('title')->pluck('title', 'id');
        return view('admin.pages.courses.scholarship.post', [
            'item'    => new Scholarship(),
            'courses' => $courses,
        ]);
    }

    public function store(Request $r)
    {
        $data = $this->validated($r);
        if ($r->hasFile('hero_image')) {
            $data['hero_image'] = $r->file('hero_image')->store('scholarships', 'public');
        }
        Scholarship::create($data);
        return redirect()->route('scholarships.index')->with('success', 'Scholarship created');
    }

    // ---------- Admin: Edit (reuses the same form) ----------
    public function edit(Scholarship $scholarship)
    {
        $courses = Course::orderBy('title')->pluck('title', 'id');
        return view('admin.pages.courses.scholarship.post', [
            'item'    => $scholarship,
            'courses' => $courses,
        ]);
    }

    public function update(Request $r, Scholarship $scholarship)
    {
        $data = $this->validated($r, $scholarship->id);
        if ($r->hasFile('hero_image')) {
            if ($scholarship->hero_image) {
                Storage::disk('public')->delete($scholarship->hero_image);
            }
            $data['hero_image'] = $r->file('hero_image')->store('scholarships', 'public');
        }
        $scholarship->update($data);
        return redirect()->route('scholarships.index')->with('success', 'Scholarship updated');
    }

    public function destroy(Scholarship $scholarship)
    {
        if ($scholarship->hero_image) {
            Storage::disk('public')->delete($scholarship->hero_image);
        }
        $scholarship->delete();
        return back()->with('success', 'Deleted');
    }

    // (optional) Admin: show a single scholarship detail page
    public function show(Scholarship $scholarship)
    {
        $scholarship->load('course');
        return view('admin.pages.courses.scholarship.viewdetails', [
            'item' => $scholarship,
        ]);
    }

    // ---------- Validation ----------
    private function validated(Request $r, $id = null): array
    {
        return $r->validate([
            'course_id'             => ['required', 'exists:courses,id'],
            'slug'                  => ['required', 'string', Rule::unique('scholarships', 'slug')->ignore($id)],
            'status'                => ['required', Rule::in(['draft', 'published', 'archived'])],
            'headline'              => ['nullable', 'string', 'max:255'],
            'subtext'               => ['nullable', 'string'],
            'text'                  => ['nullable', 'string', 'max:60'],
            'cta_url'               => ['nullable', 'string', 'max:255'],
            'about'                 => ['nullable', 'string'],
            'program_includes'      => ['nullable', 'array'],
            'program_includes.*'    => ['nullable', 'string'],
            'who_can_apply'         => ['nullable', 'array'],
            'who_can_apply.*'       => ['nullable', 'string'],
            'how_to_apply'          => ['nullable', 'array'],
            'how_to_apply.*'        => ['nullable', 'string'],
            'important_note'        => ['nullable', 'string'],
            'closing_headline'      => ['nullable', 'string', 'max:255'],
            'closing_cta_text'      => ['nullable', 'string', 'max:60'],
            'closing_cta_url'       => ['nullable', 'string', 'max:255'],
            'opens_at'              => ['nullable', 'date'],
            'closes_at'             => ['nullable', 'date', 'after_or_equal:opens_at'],
            'hero_image'            => ['nullable', 'image', 'max:4096'],
        ]);
    }

    // ---------- Public landing (unchanged) ----------
    public function display()
    {
        $scholarship = Scholarship::published()->latest()->first();
        return view('user.pages.scholarship', compact('scholarship'));
    }
}
