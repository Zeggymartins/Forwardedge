<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScolarshipApplicationController extends Controller
{
    // ---------- Public application ----------
    public function Register(CourseSchedule $schedule)
    {
        abort_unless($schedule->isFree(), 404, 'This schedule is not available for scholarship application.');

        $course = $schedule->course;

        return view('user.pages.scholarshipregistration', compact('schedule', 'course'));
    }

    public function storeData(Request $request, CourseSchedule $schedule)
    {
        abort_unless($schedule->isFree(), 404, 'This schedule is not available for scholarship application.');

        $authUser = $request->user();

        // Base rules
        $rules = [
            'why_join'   => ['required', 'string', 'max:2000'],
            'experience' => ['nullable', 'string', 'max:2000'],
            'commitment' => ['accepted'],
        ];

        // For guests, require quick-register fields
        if (!$authUser) {
            $rules = array_merge($rules, [
                'guest_name'  => ['required', 'string', 'max:255'],
                'guest_email' => ['required', 'email:rfc,dns', 'max:255'],
                'guest_phone' => ['required', 'string', 'max:40'],
            ]);
        }

        $data = $request->validate($rules);

        // Determine the user to attach the application to
        $user = $authUser;

        if (!$user) {
            // Create or reuse user by email
            $user = User::firstOrCreate(
                ['email' => $data['guest_email']],
                [
                    'name'     => $data['guest_name'],
                    'phone'    => $data['guest_phone'] ?? null,
                    'password' => bcrypt(Str::random(18)), // random password; they can reset later
                ]
            );

            // If user existed but had missing profile pieces, update them
            $updated = false;
            if (!$user->name && !empty($data['guest_name'])) {
                $user->name = $data['guest_name'];
                $updated = true;
            }
            if ((empty($user->phone) || $user->phone === '—') && !empty($data['guest_phone'])) {
                $user->phone = $data['guest_phone'];
                $updated = true;
            }
            if ($updated) $user->save();
        }

        // Prevent duplicate application for same schedule + user
        $already = ScholarshipApplication::where([
            'course_schedule_id' => $schedule->id,
            'user_id'            => $user->id,
        ])->exists();

        if ($already) {
            return redirect()
                ->route('scholarships.thankyou')
                ->with('thankyou.course', $schedule->course)
                ->with('thankyou.schedule', $schedule)
                ->with('success', 'You already applied for this cohort. We’ll email you with updates.');
        }
        $source = $authUser ? 'registered' : 'guest_to_user';
        // Create application
        DB::transaction(function () use ($schedule, $user, $data, $source) {
            ScholarshipApplication::create([
                'course_id'          => $schedule->course_id,
                'course_schedule_id' => $schedule->id,
                'user_id'            => $user->id,
                'status'             => 'pending',
                'form_data'          => [
                    'why_join'   => $data['why_join'],
                    'experience' => $data['experience'] ?? null,
                    'commitment' => true,
                    'contact'    => [
                        'name'   => $user->name,
                        'email'  => $user->email,
                        'phone'  => $user->phone ?? null,
                        'source' => $source,
                    ],
                ],
            ]);
        });


        // Send them to the Thank You page
        return redirect()
            ->route('scholarships.thankyou')
            ->with('thankyou.course', $schedule->course)
            ->with('thankyou.schedule', $schedule)
            ->with('success', 'Application submitted! We’ll notify you by email.');
    }

    // ---------- Admin: List ----------
    public function index()
    {
        $items = Scholarship::with('course')->latest()->paginate(20);
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

    public function store(Request $request)
    {
        $validated = $this->validateScholarship($request);

        foreach (['program_includes', 'who_can_apply', 'how_to_apply'] as $arrKey) {
            if (isset($validated[$arrKey]) && is_array($validated[$arrKey])) {
                $validated[$arrKey] = array_values(array_filter(
                    $validated[$arrKey],
                    fn($v) => trim((string) $v) !== ''
                ));
            }
        }

        try {
            DB::beginTransaction();

            if ($request->hasFile('hero_image')) {
                $validated['image'] = $this->storeImage($request->file('hero_image'));
            }

            Scholarship::create($validated);

            DB::commit();

            return redirect()->route('scholarships.index')
                ->with('success', 'Scholarship created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Scholarship creation failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Failed to create scholarship. Please try again.');
        }
    }

    // ---------- Admin: Edit / Update ----------
    public function edit(Scholarship $scholarship)
    {
        $courses = Course::orderBy('title')->pluck('title', 'id');
        return view('admin.pages.courses.scholarship.post', [
            'item'    => $scholarship,
            'courses' => $courses,
        ]);
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $validated = $this->validateScholarship($request, $scholarship->id);

        foreach (['program_includes', 'who_can_apply', 'how_to_apply'] as $arrKey) {
            if (isset($validated[$arrKey]) && is_array($validated[$arrKey])) {
                $validated[$arrKey] = array_values(array_filter(
                    $validated[$arrKey],
                    fn($v) => trim((string) $v) !== ''
                ));
            }
        }

        try {
            DB::beginTransaction();

            if ($request->hasFile('hero_image')) {
                $this->deleteImage($scholarship->image);
                $validated['image'] = $this->storeImage($request->file('hero_image'));
            } else {
                unset($validated['image']);
            }

            $scholarship->update($validated);

            DB::commit();

            return redirect()->route('scholarships.index')
                ->with('success', 'Scholarship updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Scholarship update failed', [
                'scholarship_id' => $scholarship->id,
                'message'        => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Failed to update scholarship. Please try again.');
        }
    }

    public function destroy(Scholarship $scholarship)
    {
        try {
            DB::beginTransaction();

            $this->deleteImage($scholarship->image);
            $scholarship->delete();

            DB::commit();

            return back()->with('success', 'Deleted');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Scholarship deletion failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Failed to delete scholarship. Please try again.');
        }
    }

    public function show(Scholarship $scholarship)
    {
        $scholarship->load('course');
        return view('admin.pages.courses.scholarship.viewdetails', ['item' => $scholarship]);
    }

    // ---------- Public landing ----------
    public function display()
    {
        $scholarship = Scholarship::published()
            ->with(['course' => fn($q) => $q->with('testimonials')])
            ->latest()
            ->first();

        abort_if(!$scholarship, 404, 'No published scholarship found.');

        return view('user.pages.scholarship', compact('scholarship'));
    }

    // ---------- Validation ----------
    private function validateScholarship(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'course_id'          => ['required', 'exists:courses,id'],
            'slug'               => ['required', 'string', 'max:255', Rule::unique('scholarships', 'slug')->ignore($id)],
            'status'             => ['required', Rule::in(['draft', 'published', 'archived'])],

            'headline'           => ['nullable', 'string', 'max:255'],
            'subtext'            => ['nullable', 'string', 'max:1000'],
            'text'               => ['nullable', 'string', 'max:60'],

            'about'              => ['nullable', 'string'],
            'program_includes'   => ['nullable', 'array'],
            'program_includes.*' => ['nullable', 'string', 'max:500'],
            'who_can_apply'      => ['nullable', 'array'],
            'who_can_apply.*'    => ['nullable', 'string', 'max:500'],
            'how_to_apply'       => ['nullable', 'array'],
            'how_to_apply.*'     => ['nullable', 'string', 'max:500'],
            'important_note'     => ['nullable', 'string', 'max:2000'],

            'closing_headline'   => ['nullable', 'string', 'max:255'],
            'closing_cta_text'   => ['nullable', 'string', 'max:60'],
            'closing_cta_url'    => ['nullable', 'url', 'max:255'],

            'opens_at'           => ['nullable', 'date'],
            'closes_at'          => ['nullable', 'date', 'after_or_equal:opens_at'],

            'hero_image'         => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
        ]);
    }

    // ---------- Storage helpers ----------
    private function storeImage(\Illuminate\Http\UploadedFile $file): string
    {
        return $file->store('scholarships', 'public');
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
