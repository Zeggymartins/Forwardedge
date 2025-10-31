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

}
