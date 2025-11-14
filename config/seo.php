<?php

return [
    'site_name' => env('APP_NAME', 'Forward Edge Consulting'),

    'defaults' => [
        'title'       => 'Forward Edge Consulting | Cybersecurity Training & Advisory',
        'description' => 'Forward Edge Consulting delivers hands-on cybersecurity training, professional services, and industry events that help African talent build world-class careers.',
        'keywords'    => 'Forward Edge Consulting, cybersecurity training, cyber security nigeria, digital forensics, GRC, tech upskilling',
        'image'       => env('SEO_DEFAULT_IMAGE', 'frontend/assets/images/logos/logo.png'),
    ],

    'routes' => [
        'home' => [
            'title'       => 'Forward Edge Consulting | Build In-Demand Cybersecurity Skills',
            'description' => 'Explore expert-led cybersecurity programs, career-launching events, and consulting support trusted by leading African organizations.',
        ],
        'about' => [
            'title'       => 'About Forward Edge Consulting | Driving Africa’s Cyber Talent',
            'description' => 'Meet the cybersecurity leaders, instructors, and strategists empowering businesses and talent across Africa.',
        ],
        'contact' => [
            'title'       => 'Contact Forward Edge Consulting',
            'description' => 'Talk to our advisory team about cybersecurity training, events, partnerships, or enterprise consulting support.',
        ],
        'academy' => [
            'title'       => 'Forward Edge Academy | Cybersecurity Learning Paths',
            'description' => 'Browse immersive cybersecurity programs covering blue team, red team, governance, risk & compliance, and leadership training.',
        ],
        'course.show' => [
            'description' => 'Deep dive into curriculum, schedules, and tuition details for our industry-aligned cybersecurity courses.',
        ],
        'services' => [
            'title'       => 'Cybersecurity Services | Forward Edge Consulting',
            'description' => 'Partner with our consultants for governance, risk & compliance, SOC build-outs, managed security, and transformation programs.',
        ],
        'services.show' => [
            'description' => 'See how this Forward Edge Consulting service helps teams strengthen cybersecurity posture and unlock new capabilities.',
        ],
        'blog' => [
            'title'       => 'Insights & Resources | Forward Edge Consulting Blog',
            'description' => 'Read expert takes on cybersecurity, digital skills, compliance, and tech leadership across Africa.',
        ],
        'blogs.show' => [
            'description' => 'Actionable cybersecurity guidance, event recaps, and skill-building advice from Forward Edge consultants.',
        ],
        'events.index' => [
            'title'       => 'Events & Training | Forward Edge Consulting',
            'description' => 'Register for cybersecurity workshops, leadership roundtables, and immersive bootcamps hosted across Africa.',
        ],
        'events.upcoming' => [
            'description' => 'Secure a seat at our upcoming cybersecurity training events before they sell out.',
        ],
        'events.featured' => [
            'description' => 'Don’t miss our flagship cybersecurity events spotlighting emerging threats and best practices.',
        ],
        'events.show' => [
            'description' => 'Agenda, speakers, and ticket info for this Forward Edge Consulting event or training program.',
        ],
        'gallery' => [
            'title'       => 'Forward Edge Consulting Gallery',
            'description' => 'Highlights from cybersecurity bootcamps, corporate trainings, and community programs.',
        ],
        'scholarships' => [
            'title'       => 'Forward Edge Scholarships',
            'description' => 'Apply for fully-funded cybersecurity cohorts designed to unlock tech careers.',
        ],
        'scholarships.apply' => [
            'description' => 'Submit your application to join this free cybersecurity cohort with Forward Edge Consulting.',
        ],
        'scholarships.apply.course' => [
            'description' => 'Tell us why you’re a fit for this scholarship-enabled course specialization.',
        ],
        'shop' => [
            'title'       => 'Course Shop | Forward Edge Consulting',
            'description' => 'Purchase cybersecurity micro-courses, masterclasses, and training bundles.',
        ],
        'shop.details' => [
            'description' => 'Learn what is included in this digital course bundle and enroll instantly.',
        ],
        'page.show' => [
            'description' => 'Explore curated resources, landing pages, and campaign experiences from Forward Edge Consulting.',
        ],
    ],
];
