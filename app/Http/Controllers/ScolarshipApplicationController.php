<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\User;
use App\Rules\Recaptcha;
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

        return view('user.pages.scholarshipregistration', array_merge(
            compact('schedule', 'course'),
            ['formOptions' => $this->formOptions()]
        ));
    }

    public function registerForCourse(Course $course)
    {
        $schedule = $course->schedules()
            ->where(function ($q) {
                $q->whereNull('price')->orWhere('price', '<=', 0);
            })
            ->orderBy('start_date')
            ->first();

        abort_if(!$schedule, 404, 'No scholarship-enabled cohort is available for this course yet.');

        return $this->Register($schedule);
    }

    public function storeData(Request $request, CourseSchedule $schedule)
    {
        abort_unless($schedule->isFree(), 404, 'This schedule is not available for scholarship application.');

        $authUser = $request->user();
        $options = $this->formOptions();

        $data = $request->validate([
            'full_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email:rfc,dns', 'max:255'],
            'phone'      => ['required', 'string', 'max:40'],
            'gender'     => ['required', Rule::in(array_keys($options['genders']))],
            'age_range'  => ['required', Rule::in(array_keys($options['age_ranges']))],
            'location'   => ['required', 'string', 'max:255'],
            'occupation_status' => ['required', Rule::in(array_keys($options['occupation_statuses']))],

            'education_level'        => ['required', Rule::in(array_keys($options['education_levels']))],
            'education_field'        => ['nullable', 'string', 'max:255'],
            'education_currently_in_school' => ['required', Rule::in(array_keys($options['yes_no']))],
            'education_institution'  => ['required_if:education_currently_in_school,yes', 'nullable', 'string', 'max:255'],
            'education_institution_level' => ['required_if:education_currently_in_school,yes', 'nullable', 'string', 'max:255'],

            'commit_available' => ['required', Rule::in(array_keys($options['commit_availability']))],
            'commit_hours'     => ['required', Rule::in(array_keys($options['commit_hours']))],
            'commit_strategy'  => ['required', 'string', 'max:2000'],

            'tech_has_laptop'  => ['required', Rule::in(array_keys($options['yes_no']))],
            'tech_laptop_specs'=> ['required_if:tech_has_laptop,yes', 'nullable', 'string', 'max:255'],
            'tech_internet'    => ['required', Rule::in(array_keys($options['internet_quality']))],
            'tech_tools'       => ['nullable', 'array'],
            'tech_tools.*'     => [Rule::in(array_keys($options['tech_tools']))],
            'tech_experience'  => ['nullable', 'string', 'max:2000'],

            'motivation_reason'          => ['required', 'string', 'max:2000'],
            'motivation_future'          => ['required', 'string', 'max:2000'],
            'motivation_prev_training'   => ['required', Rule::in(array_keys($options['yes_no']))],
            'motivation_prev_details'    => ['required_if:motivation_prev_training,yes', 'nullable', 'string', 'max:2000'],
            'motivation_unselected_plan' => ['required', Rule::in(array_keys($options['motivation_unselected_plan']))],
            'motivation_interest_area'   => ['required', Rule::in(array_keys($options['motivation_interest_areas']))],
            'motivation_interest_other'  => ['required_if:motivation_interest_area,other', 'nullable', 'string', 'max:255'],

            'skill_level'            => ['required', Rule::in(array_keys($options['skill_levels']))],
            'skill_project_response' => ['required', Rule::in(array_keys($options['skill_project_responses']))],
            'skill_familiarity'      => ['required', Rule::in(array_keys($options['skill_familiarity']))],

            'attitude_teamwork'          => ['required', 'string', 'max:2000'],
            'attitude_participation'     => ['required', Rule::in(array_keys($options['yes_no']))],
            'attitude_discovery_channel' => ['required', Rule::in(array_keys($options['discovery_channels']))],
            'attitude_commitment'        => ['required', Rule::in(array_keys($options['yes_no']))],

            'bonus_willing_challenge'    => ['required', Rule::in(array_keys($options['yes_no']))],

            'recaptcha_token' => ['required', new Recaptcha('scholarship_form')],
        ]);

        // Determine the user to attach the application to
        $user = $authUser;
        $contactEmail = strtolower($data['email']);
        $contactName  = $data['full_name'];
        $contactPhone = $data['phone'];

        if (!$user) {
            // Create or reuse user by email
            $user = User::firstOrCreate(
                ['email' => $contactEmail],
                [
                    'name'     => $contactName,
                    'phone'    => $contactPhone ?? null,
                    'password' => bcrypt(Str::random(18)), // random password; they can reset later
                ]
            );

            // If user existed but had missing profile pieces, update them
            $updated = false;
            if (!$user->name && !empty($contactName)) {
                $user->name = $contactName;
                $updated = true;
            }
            if ((empty($user->phone) || $user->phone === '—') && !empty($contactPhone)) {
                $user->phone = $contactPhone;
                $updated = true;
            }
            if ($updated) $user->save();
        } else {
            $updates = [];
            if (!$user->name && !empty($contactName)) {
                $updates['name'] = $contactName;
            }
            if ((empty($user->phone) || $user->phone === '—') && !empty($contactPhone)) {
                $updates['phone'] = $contactPhone;
            }
            if ($updates) {
                $user->fill($updates)->save();
            }
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
        DB::transaction(function () use ($schedule, $user, $data, $source, $options, $contactName, $contactEmail, $contactPhone) {
            $formPayload = [
                'personal' => [
                    'full_name' => $contactName,
                    'email'     => $contactEmail,
                    'phone'     => $contactPhone,
                    'gender'    => $data['gender'],
                    'age_range' => $data['age_range'],
                    'location'  => $data['location'],
                    'occupation_status' => $data['occupation_status'],
                ],
                'education' => [
                    'highest_level' => $data['education_level'],
                    'field'         => $data['education_field'] ?? null,
                    'currently_in_school' => $data['education_currently_in_school'],
                    'institution'   => $data['education_institution'] ?? null,
                    'institution_level' => $data['education_institution_level'] ?? null,
                ],
                'commitment' => [
                    'availability' => $data['commit_available'],
                    'availability_label' => $options['commit_availability'][$data['commit_available']] ?? $data['commit_available'],
                    'hours_per_week' => $data['commit_hours'],
                    'hours_label'    => $options['commit_hours'][$data['commit_hours']] ?? $data['commit_hours'],
                    'consistency_plan' => $data['commit_strategy'],
                ],
                'technical' => [
                    'has_laptop'    => $data['tech_has_laptop'],
                    'laptop_specs'  => $data['tech_laptop_specs'] ?? null,
                    'internet'      => $data['tech_internet'],
                    'internet_label'=> $options['internet_quality'][$data['tech_internet']] ?? $data['tech_internet'],
                    'tools'         => $data['tech_tools'] ?? [],
                    'experience'    => $data['tech_experience'] ?? null,
                ],
                'motivation' => [
                    'reason'        => $data['motivation_reason'],
                    'future_plan'   => $data['motivation_future'],
                    'previous_training' => $data['motivation_prev_training'],
                    'previous_training_details' => $data['motivation_prev_details'] ?? null,
                    'plan_if_not_selected' => $data['motivation_unselected_plan'],
                    'interest_area'        => $data['motivation_interest_area'],
                    'interest_area_other'  => $data['motivation_interest_other'] ?? null,
                ],
                'skills' => [
                    'level'             => $data['skill_level'],
                    'project_response'  => $data['skill_project_response'],
                    'familiarity'       => $data['skill_familiarity'],
                ],
                'attitude' => [
                    'teamwork_style'        => $data['attitude_teamwork'],
                    'participation'         => $data['attitude_participation'],
                    'discovery_channel'     => $data['attitude_discovery_channel'],
                    'commitment_agreement'  => $data['attitude_commitment'],
                ],
                'bonus' => [
                    'challenge_opt_in' => $data['bonus_willing_challenge'],
                ],
                'contact' => [
                    'name'   => $contactName,
                    'email'  => $contactEmail,
                    'phone'  => $contactPhone,
                    'source' => $source,
                ],
                // legacy keys for backward compatibility
                'why_join'   => $data['motivation_reason'],
                'experience' => $data['tech_experience'] ?? null,
            ];

            ScholarshipApplication::create([
                'course_id'          => $schedule->course_id,
                'course_schedule_id' => $schedule->id,
                'user_id'            => $user->id,
                'status'             => 'pending',
                'form_data'          => $formPayload,
            ]);
        });


        // Send them to the Thank You page
        return redirect()
            ->route('scholarships.thankyou')
            ->with('thankyou.course', $schedule->course)
            ->with('thankyou.schedule', $schedule)
            ->with('success', 'Application submitted! We’ll notify you by email.');
    }

    protected function formOptions(): array
    {
        return config('scholarship.form_options', []);
    }
}
