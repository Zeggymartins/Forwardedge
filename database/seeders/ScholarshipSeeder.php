<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Scholarship;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ScholarshipSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have some courses to attach to
        if (Course::count() === 0) {
            $seedCourses = [
                ['title' => 'Cybersecurity Bootcamp', 'slug' => 'cybersecurity-bootcamp', 'status' => 'published', 'price' => 0],
                ['title' => 'Data Analytics Pro',      'slug' => 'data-analytics-pro',     'status' => 'published', 'price' => 0],
                ['title' => 'Full-Stack Web Dev',      'slug' => 'full-stack-web-dev',     'status' => 'published', 'price' => 0],
            ];

            foreach ($seedCourses as $c) {
                Course::firstOrCreate(
                    ['slug' => $c['slug']],
                    [
                        'title'       => $c['title'],
                        'status'      => $c['status'],
                        'price'       => $c['price'],
                        'description' => 'Seeded course for scholarship testing.',
                    ]
                );
            }
        }

        $courses = Course::orderBy('id')->get();
        if ($courses->isEmpty()) {
            $this->command->warn('No courses available; skipping Scholarship seeding.');
            return;
        }

        // Helper to pick a course id in rotation
        $courseId = fn(int $i) => $courses[$i % $courses->count()]->id;

        // Sample payloads
        $now = Carbon::now();

        $rows = [
            [
                'course_id'        => $courseId(0),
                'slug'             => 'cybersecurity-bootcamp-50-scholarship',
                'status'           => 'published',
                'headline'         => 'Forward Edge Cybersecurity Bootcamp 5.0 — Scholarship',
                'subtext'          => 'Get hands-on skills, pay ₦0 upfront. Limited slots.',
                'text'             => 'Apply Now',
                'cta_url'          => '/scholarship', // or route('scholarship.landing')
                'about'            => 'This scholarship supports aspiring cybersecurity professionals with tuition-free training and career support.',
                'program_includes' => [
                    'Live mentor-led classes (evenings/weekends)',
                    'Real-world capstone projects',
                    'Career coaching and interview prep',
                    'Community & alumni network',
                ],
                'who_can_apply'    => [
                    'Beginners or juniors looking to pivot into Cybersecurity',
                    'NYSC / fresh graduates seeking employable skills',
                    'Working professionals switching careers',
                    'Committed self-starters able to complete weekly tasks',
                ],
                'how_to_apply'     => [
                    'Fill out the application form (motivation + background).',
                    'Complete an aptitude/tech readiness check (email sent after form).',
                    'Attend a short interview with our admissions team.',
                    'Get your admission decision by email.',
                ],
                'important_note'   => 'Attendance and weekly task completion are required to retain the scholarship.',
                'closing_headline' => 'Ready to Get Started?',
                'closing_cta_text' => 'Apply for Scholarship',
                'closing_cta_url'  => '/scholarship',
                'opens_at'         => $now->copy()->subDays(3),
                'closes_at'        => $now->copy()->addWeeks(2),
                // 'hero_image'     => uploaded later (leave null in seed)
            ],
            [
                'course_id'        => $courseId(1),
                'slug'             => 'data-analytics-access-scholarship',
                'status'           => 'draft',
                'headline'         => 'Data Analytics Access Scholarship',
                'subtext'          => 'For aspiring data analysts — learn Excel, SQL, BI, and storytelling.',
                'text'             => 'Get Early Access',
                'cta_url'          => '/scholarship',
                'about'            => 'A merit-based scholarship enabling candidates to gain strong foundations in data analytics.',
                'program_includes' => [
                    'Excel & Google Sheets fundamentals',
                    'SQL for data querying',
                    'Data visualization (Power BI / Tableau)',
                    'Analytics storytelling & case studies',
                ],
                'who_can_apply'    => [
                    'Anyone with basic spreadsheet familiarity',
                    'Recent graduates or career switchers',
                    'Professionals in non-tech roles looking to upskill',
                ],
                'how_to_apply'     => [
                    'Submit your short statement of interest.',
                    'Complete a basic numeracy/logic quiz.',
                    'Receive outcome within 7 days.',
                ],
                'important_note'   => 'Laptop and stable internet required for participation.',
                'closing_headline' => 'Get on the shortlist',
                'closing_cta_text' => 'Join Waitlist',
                'closing_cta_url'  => '/scholarship',
                'opens_at'         => $now->copy()->addDays(2),
                'closes_at'        => $now->copy()->addWeeks(3),
            ],
            [
                'course_id'        => $courseId(2),
                'slug'             => 'full-stack-dev-impact-scholarship',
                'status'           => 'archived',
                'headline'         => 'Full-Stack Dev Impact Scholarship (Closed)',
                'subtext'          => 'Scholarship round closed — check back soon.',
                'text'             => 'View Course',
                'cta_url'          => '/academy',
                'about'            => 'Previously offered to support learners in HTML/CSS/JS, Laravel, and React fundamentals.',
                'program_includes' => [
                    'Modern HTML/CSS & responsive design',
                    'JavaScript fundamentals + ES6',
                    'Backend with Laravel (API design)',
                    'Frontend with React (components, state)',
                ],
                'who_can_apply'    => [
                    'Applicants with basic computer literacy',
                    'Learners who can commit 10–12 hrs weekly',
                ],
                'how_to_apply'     => [
                    'Online application form',
                    'Coding readiness check',
                    'Final selection email',
                ],
                'important_note'   => 'This round is closed. Watch out for the next cycle.',
                'closing_headline' => 'Missed this round?',
                'closing_cta_text' => 'View All Courses',
                'closing_cta_url'  => '/academy',
                'opens_at'         => $now->copy()->subMonths(2),
                'closes_at'        => $now->copy()->subMonths(1),
            ],
        ];

        foreach ($rows as $row) {
            // Guarantee unique slug if seeding multiple times
            $slug = $row['slug'];
            $base = $slug;
            $n = 1;
            while (Scholarship::where('slug', $slug)->exists()) {
                $slug = $base . '-' . $n++;
            }
            $row['slug'] = $slug;

            Scholarship::create($row);
        }

        $this->command->info('Scholarships seeded successfully.');
    }
}
