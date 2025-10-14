<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\CourseDetails;
use App\Models\CoursePhases;
use App\Models\CourseTopics;
use App\Models\CourseSchedule;

class CourseBootcampSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1) Create Course
            $title = 'Bootcamp 5.0: Global Cybersecurity Training';
            $slug  = Str::slug($title);
            $base  = $slug;
            $i = 1;
            while (Course::where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }

            $course = Course::create([
                'title'       => $title,
                'slug'        => $slug,
                'description' => "We’re training 1,000 participants worldwide in cybersecurity foundations, with a strong focus on Africa.",
                'thumbnail'   => 'courses/thumbnails/bootcamp5-hero-fake.jpg', // placeholder
                'status'      => 'published',
            ]);

            // 2) Details (strict types)
            $order = 1;
            $details = [
                // header and paragraph
                ['type' => 'heading',   'content' => 'Bootcamp 5.0: Global Cybersecurity Training, Africa Leading the Way'],
                ['type' => 'paragraph', 'content' => "We’re training 1,000 participants worldwide in cybersecurity foundations, with a strong focus on Africa. 5 weeks, 15 live classes, hands-on labs. Didn’t get selected? You can still enroll directly."],
                // (optional) hero image
                ['type' => 'image',     'content' => null, 'image' => 'courses/details/images/hero-banner-fake.jpg'],
                ['type' => 'image',     'content' => null, 'image' => 'courses/details/images/hero-banner-fake.jpg'],


                // header header and list
                ['type' => 'heading',   'content' => 'The Hybrid Model for Cyber Careers'],
                ['type' => 'list',      'content' => json_encode([
                    '5 Weeks Live Foundations – 15 classes, 3x per week',
                    '3 Specialization Tracks – Self-paced courses in Pentesting, SOC, or GRC',
                    'Hands-on Labs – Practical exercises, tools, and case studies',
                    'Flexible Pathway – Learn live, specialize later, at your own pace',
                ], JSON_UNESCAPED_UNICODE)],

                // header and features (Flow Section)
                ['type' => 'heading',   'content' => 'How It Works (Flow Section)'],
                ['type' => 'features',  'content' => [
                    [
                        'heading' => 'Live Foundations (5 Weeks • 15 Classes)',
                        'description' => "Interactive teaching and projects\nLabs in Windows, Linux, networking and cryptography\nCertificate of completion",
                    ],
                    [
                        'heading' => 'Choose Your Track (Pentesting • SOC • GRC)',
                        'description' => '',
                    ],
                    [
                        'heading' => 'Self-Paced Specialization (Lifetime Access)',
                        'description' => "Pre-recorded lessons\nDownloadable labs\nFlexible timelines\nCareer-ready certificate",
                    ],
                ]],

                // paragraph heading heading paragraph list (Foundational Training block)
                ['type' => 'paragraph', 'content' => '“Start strong with live training. Grow deeper with flexible, self-paced tracks.”'],
                ['type' => 'heading',   'content' => 'Foundational Training (5 Weeks, 15 Live Classes)'],
                ['type' => 'heading',   'content' => 'Your Launchpad into Cybersecurity'],
                ['type' => 'paragraph', 'content' => 'Learn the essential skills every cybersecurity professional needs.'],
                ['type' => 'heading',   'content' => 'What you’ll master:'],
                ['type' => 'list',      'content' => json_encode([
                    'Cybersecurity fundamentals and attack types',
                    'Windows and Linux basics plus Active Directory',
                    'Command line, PowerShell and Bash scripting',
                    'Networking concepts, protocols and analysis',
                    'Tools: Wireshark, tcpdump, Nmap',
                    'Cryptography, hashing, password security and cracking labs',
                ], JSON_UNESCAPED_UNICODE)],

                // heading list (Outcomes)
                ['type' => 'heading',   'content' => 'Outcomes'],
                ['type' => 'list',      'content' => json_encode([
                    'Confident with Windows and Linux systems',
                    'Able to analyze traffic with Wireshark and Nmap',
                    'Understand core protocols and cryptography',
                    'Hands-on project experience',
                    'Certificate of Completion',
                ], JSON_UNESCAPED_UNICODE)],

                // paragraph (pricing + scholarship)
                ['type' => 'paragraph', 'content' => "Value: ₦150,000 / \$150\nFree for 1,000 selected participants"],

                // heading paragraph heading and list (Specialization overview)
                ['type' => 'heading',   'content' => 'Specialization Tracks (Self-Paced)'],
                ['type' => 'paragraph', 'content' => 'Advance your career in the direction that excites you most.'],
                ['type' => 'heading',   'content' => 'How It Works:'],
                ['type' => 'list',      'content' => json_encode([
                    '100% online and flexible',
                    'Pre-recorded, structured lessons',
                    'Downloadable lab guides and exercises',
                    'Learn anytime, at your own pace',
                    'Lifetime access to all content',
                    'Certificate on completion',
                ], JSON_UNESCAPED_UNICODE)],

                // features (three tracks)
                ['type' => 'features',  'content' => [
                    [
                        'heading' => 'Penetration Testing and Ethical Hacking',
                        'description' => "Reconnaissance, scanning, exploitation\nVulnerability testing and web/app pentesting\nLabs in Kali Linux (Metasploit, Nmap, Burp Suite)\nCareer Outcomes: Junior Pen Tester, Ethical Hacker\nCTA: Enroll in Penetration Testing (Self-Paced)",
                    ],
                    [
                        'heading' => 'Security Operations (SOC and Incident Response)',
                        'description' => "SIEM, log analysis and threat detection\nIncident response playbooks\nTools: Splunk/ELK, Security Onion, YARA, MISP\nCareer Outcomes: SOC Analyst, Threat Hunter, Cybersecurity Analyst\nCTA: Enroll in SOC and Incident Response (Self-Paced)",
                    ],
                    [
                        'heading' => 'Governance, Risk and Compliance (GRC)',
                        'description' => "Security frameworks (ISO, NIST, NDPR)\nRisk assessment, gap analysis and audits\nReal-world compliance projects\nCareer Outcomes: GRC Analyst, Compliance Specialist\nCTA: Enroll in GRC (Self-Paced)",
                    ],
                ]],

                // paragraph (specialization pricing + bundle)
                ['type' => 'paragraph', 'content' => "Each specialization: ₦120,000 / \$120\nBundle (All 3 Tracks): ₦300,000 / \$300\nGet All 3 Specializations (Save ₦60,000 / \$60)"],

                // heading and list (Bundle Options)
                ['type' => 'heading',   'content' => 'Bundle Options'],
                ['type' => 'list',      'content' => json_encode([
                    'Foundations + 1 Specialization: ₦250,000 / $250',
                    'Foundations + All 3 Specializations: ₦350,000 / $350',
                ], JSON_UNESCAPED_UNICODE)],

                // heading and list (Why Join Us)
                ['type' => 'heading',   'content' => 'Why Join Us'],
                ['type' => 'list',      'content' => json_encode([
                    '300+ trained in past bootcamps',
                    'Alumni who have landed real cyber roles',
                    'Hands-on labs and real-world case studies',
                    'Rooted in Africa, connected globally',
                ], JSON_UNESCAPED_UNICODE)],

                // heading heading and paragraph (closing)
                ['type' => 'heading',   'content' => 'Ready to start your cybersecurity career?'],
                ['type' => 'heading',   'content' => 'Foundations are live. Specializations are flexible.'],
                ['type' => 'paragraph', 'content' => 'Your future in cyber starts here.'],
            ];

            foreach ($details as $row) {
                CourseDetails::create([
                    'course_id'  => $course->id,
                    'type'       => $row['type'],
                    'sort_order' => $order++,
                    'content'    => $row['content'] ?? null,   // strings or arrays (features) or JSON strings (list)
                    'image'      => $row['image']  ?? null,    // only for images
                ]);
            }

            // 3) Sample Phases + Topics (kept modest)
            $phases = [
                [
                    'title'    => 'Phase 1: Windows & Linux Basics',
                    'duration' => 7,
                    'content'  => 'System fundamentals and Active Directory intro.',
                    'image'    => 'courses/phases/phase1-fake.jpg',
                    'topics'   => [
                        ['title' => 'Windows Basics',         'content' => 'User/admin tasks, services, AD basics'],
                        ['title' => 'Linux Basics',           'content' => 'Filesystem, users, permissions, shell'],
                        ['title' => 'Active Directory Intro', 'content' => 'Domains, OU, policies'],
                    ],
                ],
                [
                    'title'    => 'Phase 2: Networking & Tools',
                    'duration' => 10,
                    'content'  => 'Networking concepts, packet analysis, and scanners.',
                    'image'    => 'courses/phases/phase2-fake.jpg',
                    'topics'   => [
                        ['title' => 'Networking Core',     'content' => 'OSI, TCP/IP, protocols'],
                        ['title' => 'Wireshark & tcpdump', 'content' => 'Traffic capture and analysis'],
                        ['title' => 'Nmap',                'content' => 'Scanning, service detection'],
                    ],
                ],
                [
                    'title'    => 'Phase 3: Scripting & Cryptography',
                    'duration' => 8,
                    'content'  => 'Automation, hashing, passwords, and cryptography basics.',
                    'image'    => 'courses/phases/phase3-fake.jpg',
                    'topics'   => [
                        ['title' => 'PowerShell & Bash',      'content' => 'Automation and scripting basics'],
                        ['title' => 'Cryptography & Hashing', 'content' => 'Hashes, salts, cracking labs'],
                    ],
                ],
            ];

            foreach ($phases as $pIndex => $p) {
                $phase = CoursePhases::create([
                    'course_id' => $course->id,
                    'title'     => $p['title'],
                    'order'     => $pIndex + 1,
                    'duration'  => $p['duration'],
                    'content'   => $p['content'],
                    'image'     => $p['image'],
                ]);

                foreach ($p['topics'] as $tIndex => $t) {
                    CourseTopics::create([
                        'course_phase_id' => $phase->id,
                        'title'  => $t['title'],
                        'content' => $t['content'] ?? null,
                        'order'  => $tIndex + 1,
                    ]);
                }
            }

            // 4) Schedules
            CourseSchedule::create([
                'course_id'  => $course->id,
                'start_date' => now()->addWeeks(2)->toDateString(),
                'end_date'   => now()->addWeeks(7)->toDateString(),
                'location'   => 'Online (Global)',
                'type'       => 'virtual',
                'price'      => 150000,
            ]);

            CourseSchedule::create([
                'course_id'  => $course->id,
                'start_date' => now()->addMonths(2)->toDateString(),
                'end_date'   => now()->addMonths(3)->toDateString(),
                'location'   => 'Lagos, Nigeria',
                'type'       => 'hybrid',
                'price'      => 150000,
            ]);
        });
    }
}
