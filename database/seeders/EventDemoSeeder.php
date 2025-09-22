<?php

// database/seeders/EventDemoSeeder.php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventContent;
use App\Models\EventSpeaker;
use App\Models\EventSchedule;
use App\Models\EventTicket;
use App\Models\EventSponsor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventDemoSeeder extends Seeder
{
    public function run()
    {
        // Create main event
        $event = Event::create([
            'title' => 'Digital Transformation Summit 2024',
            'slug' => 'digital-transformation-summit-2024',
            'short_description' => 'Join industry leaders, innovators, and digital transformation experts for a comprehensive two-day summit exploring the future of business technology, AI integration, and strategic digital initiatives.',
            'location' => 'Lagos, Nigeria',
            'venue' => 'Eko Convention Center',
            'start_date' => Carbon::now()->addMonth(2)->setHour(9)->setMinute(0),
            'end_date' => Carbon::now()->addMonth(2)->addDay(1)->setHour(17)->setMinute(0),
            'status' => 'published',
            'type' => 'conference',
            'price' => 150.00,
            'max_attendees' => 500,
            'current_attendees' => 87,
            'organizer_name' => 'Forward Edge Consulting',
            'organizer_email' => 'events@forwardedge.com',
            'contact_phone' => '+234-801-234-5678',
            'is_featured' => true,
            'meta_description' => 'Premier digital transformation conference in Lagos, Nigeria',
        ]);

        // Create speakers first
        $speakers = [
            [
                'name' => 'Dr. Amina Hassan',
                'title' => 'Chief Digital Officer',
                'company' => 'TechAdvance Nigeria',
                'bio' => 'Leading digital transformation initiatives across Africa with over 15 years of experience in enterprise technology.',
                'is_keynote' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Michael Chen',
                'title' => 'AI Strategy Director',
                'company' => 'Global Innovation Labs',
                'bio' => 'Pioneering artificial intelligence implementations in emerging markets and enterprise solutions.',
                'is_keynote' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Sarah Williams',
                'title' => 'VP of Digital Strategy',
                'company' => 'ConsultPro International',
                'bio' => 'Expert in organizational change management and digital adoption strategies.',
                'is_keynote' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'David Okonkwo',
                'title' => 'Fintech Innovation Lead',
                'company' => 'PayTech Solutions',
                'bio' => 'Revolutionizing financial services through digital innovation and blockchain technology.',
                'is_keynote' => false,
                'sort_order' => 4,
            ]
        ];

        foreach ($speakers as $speakerData) {
            EventSpeaker::create(array_merge(['event_id' => $event->id], $speakerData));
        }

        // Create dynamic content blocks
        $contents = [
            // Introduction paragraph
            [
                'type' => 'paragraph',
                'content' => 'The Digital Transformation Summit 2024 brings together the brightest minds in technology, business strategy, and innovation. Over two intensive days, attendees will explore cutting-edge digital solutions, learn from real-world case studies, and network with peers who are driving change across industries.',
                'sort_order' => 1,
            ],

            // Key topics heading
            [
                'type' => 'heading',
                'content' => 'Key Summit Topics',
                'position' => 1,
                'sort_order' => 2,
            ],

            // Topics list (two columns)
            [
                'type' => 'list',
                'content' => json_encode([
                    'Artificial Intelligence & Machine Learning',
                    'Cloud Migration Strategies',
                    'Digital Customer Experience',
                    'Cybersecurity in Digital Age',
                    'Data Analytics & Business Intelligence',
                    'Process Automation & RPA',
                    'Change Management Best Practices',
                    'ROI Measurement & KPIs'
                ]),
                'sort_order' => 3,
            ],

            // Why attend heading
            [
                'type' => 'heading',
                'content' => 'Why Attend This Summit?',
                'position' => 1,
                'sort_order' => 4,
            ],

            // Features (cards)
            [
                'type' => 'feature',
                'content' => json_encode([
                    'number' => '01.',
                    'title' => 'Expert-Led Sessions',
                    'description' => 'Learn from industry leaders who have successfully implemented digital transformation initiatives in organizations of all sizes.'
                ]),
                'sort_order' => 5,
            ],
            [
                'type' => 'feature',
                'content' => json_encode([
                    'number' => '02.',
                    'title' => 'Networking Opportunities',
                    'description' => 'Connect with like-minded professionals, potential partners, and industry experts during dedicated networking sessions.'
                ]),
                'sort_order' => 6,
            ],
            [
                'type' => 'feature',
                'content' => json_encode([
                    'number' => '03.',
                    'title' => 'Practical Workshops',
                    'description' => 'Participate in hands-on workshops designed to give you actionable strategies you can implement immediately.'
                ]),
                'sort_order' => 7,
            ],

            // Speaker spotlight
            [
                'type' => 'speaker',
                'content' => json_encode(['featured' => true]),
                'sort_order' => 8,
            ],

            // Schedule section
            [
                'type' => 'schedule',
                'content' => json_encode(['display' => 'full']),
                'sort_order' => 9,
            ],

            // Gallery heading
            [
                'type' => 'heading',
                'content' => 'Summit Gallery',
                'position' => 1,
                'sort_order' => 10,
            ],

            // Image gallery (simulate multiple images)
            [
                'type' => 'image',
                'content' => json_encode([
                    'event/summit-main-hall.webp',
                    'event/networking-session.webp',
                    'event/workshop-demo.webp'
                ]),
                'sort_order' => 11,
            ],

            // Tickets section
            [
                'type' => 'ticket',
                'content' => json_encode(['display' => 'all']),
                'sort_order' => 12,
            ],

            // Sponsors section
            [
                'type' => 'sponsor',
                'content' => json_encode(['display' => 'all']),
                'sort_order' => 13,
            ],
        ];

        foreach ($contents as $contentData) {
            EventContent::create(array_merge(['event_id' => $event->id], $contentData));
        }

        // Create schedule
        $schedules = [
            // Day 1
            [
                'schedule_date' => $event->start_date->toDateString(),
                'start_time' => '09:00',
                'end_time' => '09:30',
                'session_title' => 'Registration & Welcome Coffee',
                'description' => 'Check-in and networking breakfast',
                'session_type' => 'networking',
                'sort_order' => 1,
            ],
            [
                'schedule_date' => $event->start_date->toDateString(),
                'start_time' => '09:30',
                'end_time' => '10:30',
                'session_title' => 'Opening Keynote: The Future of Digital Business',
                'description' => 'Understanding the digital landscape and emerging trends',
                'speaker_name' => 'Dr. Amina Hassan',
                'location' => 'Main Auditorium',
                'session_type' => 'keynote',
                'sort_order' => 2,
            ],
            [
                'schedule_date' => $event->start_date->toDateString(),
                'start_time' => '10:45',
                'end_time' => '11:45',
                'session_title' => 'AI Integration Strategies',
                'description' => 'Practical approaches to implementing AI in business operations',
                'speaker_name' => 'Michael Chen',
                'location' => 'Hall A',
                'session_type' => 'session',
                'sort_order' => 3,
            ],
            [
                'schedule_date' => $event->start_date->toDateString(),
                'start_time' => '12:00',
                'end_time' => '13:00',
                'session_title' => 'Panel: Change Management in Digital Transformation',
                'description' => 'Expert panel discussion on overcoming organizational resistance',
                'speaker_name' => 'Sarah Williams & Panel',
                'location' => 'Main Auditorium',
                'session_type' => 'session',
                'sort_order' => 4,
            ],
            [
                'schedule_date' => $event->start_date->toDateString(),
                'start_time' => '13:00',
                'end_time' => '14:00',
                'session_title' => 'Networking Lunch',
                'description' => 'Lunch and networking opportunity',
                'session_type' => 'lunch',
                'sort_order' => 5,
            ],
            [
                'schedule_date' => $event->start_date->toDateString(),
                'start_time' => '14:00',
                'end_time' => '15:30',
                'session_title' => 'Workshop: Digital Strategy Development',
                'description' => 'Hands-on workshop for creating digital transformation roadmaps',
                'speaker_name' => 'Multiple Facilitators',
                'location' => 'Workshop Rooms 1-3',
                'session_type' => 'workshop',
                'sort_order' => 6,
            ],

            // Day 2
            [
                'schedule_date' => $event->end_date->toDateString(),
                'start_time' => '09:00',
                'end_time' => '10:00',
                'session_title' => 'Fintech Innovation in Africa',
                'description' => 'Exploring financial technology trends and opportunities',
                'speaker_name' => 'David Okonkwo',
                'location' => 'Main Auditorium',
                'session_type' => 'keynote',
                'sort_order' => 7,
            ],
            [
                'schedule_date' => $event->end_date->toDateString(),
                'start_time' => '10:15',
                'end_time' => '11:15',
                'session_title' => 'Cybersecurity for Digital Organizations',
                'description' => 'Security considerations in digital transformation',
                'location' => 'Hall B',
                'session_type' => 'session',
                'sort_order' => 8,
            ],
            [
                'schedule_date' => $event->end_date->toDateString(),
                'start_time' => '11:30',
                'end_time' => '12:30',
                'session_title' => 'Measuring Digital Transformation ROI',
                'description' => 'Metrics and KPIs for digital initiatives',
                'location' => 'Hall A',
                'session_type' => 'session',
                'sort_order' => 9,
            ],
            [
                'schedule_date' => $event->end_date->toDateString(),
                'start_time' => '14:00',
                'end_time' => '15:00',
                'session_title' => 'Closing Keynote & Next Steps',
                'description' => 'Summit wrap-up and future outlook',
                'location' => 'Main Auditorium',
                'session_type' => 'keynote',
                'sort_order' => 10,
            ],
        ];

        foreach ($schedules as $scheduleData) {
            EventSchedule::create(array_merge(['event_id' => $event->id], $scheduleData));
        }

        // Create ticket types
        $tickets = [
            [
                'name' => 'Early Bird',
                'description' => 'Limited time offer for early registrants',
                'price' => 120.00,
                'quantity_available' => 100,
                'quantity_sold' => 67,
                'sale_end' => Carbon::now()->addWeeks(3),
                'features' => [
                    'Access to all sessions',
                    'Welcome kit & materials',
                    'Lunch & refreshments',
                    'Networking events',
                    'Certificate of attendance'
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Regular',
                'description' => 'Standard conference access',
                'price' => 150.00,
                'quantity_available' => 300,
                'quantity_sold' => 20,
                'features' => [
                    'Access to all sessions',
                    'Welcome kit & materials',
                    'Lunch & refreshments',
                    'Certificate of attendance'
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'VIP',
                'description' => 'Premium experience with exclusive benefits',
                'price' => 250.00,
                'quantity_available' => 50,
                'quantity_sold' => 12,
                'features' => [
                    'Access to all sessions',
                    'Premium welcome kit',
                    'VIP lunch & refreshments',
                    'Exclusive networking dinner',
                    'One-on-one meeting opportunities',
                    'Priority seating',
                    'Certificate of attendance'
                ],
                'sort_order' => 3,
            ],
        ];

        foreach ($tickets as $ticketData) {
            EventTicket::create(array_merge(['event_id' => $event->id], $ticketData));
        }

        // Create sponsors
        $sponsors = [
            [
                'name' => 'Microsoft Azure',
                'tier' => 'platinum',
                'website' => 'https://azure.microsoft.com',
                'description' => 'Cloud computing platform and services',
                'sort_order' => 1,
            ],
            [
                'name' => 'IBM Watson',
                'tier' => 'gold',
                'website' => 'https://www.ibm.com/watson',
                'description' => 'AI and machine learning solutions',
                'sort_order' => 2,
            ],
            [
                'name' => 'Salesforce',
                'tier' => 'gold',
                'website' => 'https://www.salesforce.com',
                'description' => 'Customer relationship management platform',
                'sort_order' => 3,
            ],
            [
                'name' => 'Tech Nation Nigeria',
                'tier' => 'silver',
                'website' => 'https://technation.ng',
                'description' => 'Supporting Nigeria tech ecosystem',
                'sort_order' => 4,
            ],
            [
                'name' => 'Lagos Chamber of Commerce',
                'tier' => 'bronze',
                'website' => 'https://lagoschamber.com',
                'description' => 'Business development and networking',
                'sort_order' => 5,
            ],
            [
                'name' => 'StartupLagos',
                'tier' => 'bronze',
                'website' => 'https://startuplagos.ng',
                'description' => 'Supporting startup ecosystem',
                'sort_order' => 6,
            ],
        ];

        foreach ($sponsors as $sponsorData) {
            EventSponsor::create(array_merge(['event_id' => $event->id], $sponsorData));
        }

        $this->command->info('Event demo data created successfully!');
        $this->command->info('Event: ' . $event->title);
        $this->command->info('Speakers: ' . $event->speakers->count());
        $this->command->info('Schedule items: ' . $event->schedules->count());
        $this->command->info('Ticket types: ' . $event->tickets->count());
        $this->command->info('Sponsors: ' . $event->sponsors->count());
        $this->command->info('Content blocks: ' . $event->contents->count());
    }
}
