<?php

return [
    'form_options' => [
        'genders' => [
            'female' => 'Female',
            'male' => 'Male',
            'non_binary' => 'Non-binary',
            'prefer_not' => 'Prefer not to say',
        ],
        'age_ranges' => [
            '15-20' => '15–20',
            '21-25' => '21–25',
            '26-30' => '26–30',
            '31+'   => '31+',
        ],
        'occupation_statuses' => [
            'student' => 'Student',
            'graduate' => 'Recent Graduate',
            'working_professional' => 'Working Professional',
            'entrepreneur' => 'Entrepreneur / Freelancer',
            'other' => 'Other',
        ],
        'education_levels' => [
            'secondary' => 'Secondary / High School',
            'diploma'   => 'Diploma / OND / HND',
            'bachelors' => "Bachelor's Degree",
            'masters'   => "Master's Degree",
            'doctorate' => 'Doctorate',
            'other'     => 'Other',
        ],
        'yes_no' => [
            'yes' => 'Yes',
            'no'  => 'No',
        ],
        'commit_availability' => [
            'yes_full' => 'Yes, I can commit fully',
            'maybe'    => 'Maybe, depending on my schedule',
            'not_sure' => "No, I'm not sure",
        ],
        'commit_hours' => [
            'lt3'   => 'Less than 3 hours',
            '3_6'   => '3–6 hours',
            '7_10'  => '7–10 hours',
            'gt10'  => 'More than 10 hours',
        ],
        'internet_quality' => [
            'stable'   => 'Yes, stable internet',
            'moderate' => 'Moderate connection (sometimes slow)',
            'poor'     => 'No, I struggle with internet access',
        ],
        'tech_tools' => [
            'terminal'      => 'Command Prompt / Terminal',
            'virtualization'=> 'VirtualBox / VMware',
            'kali'          => 'Kali Linux / Parrot OS',
            'wireshark'     => 'Wireshark / Nmap',
            'none'          => 'None of the above',
        ],
        'motivation_unselected_plan' => [
            'self_learn' => 'Keep learning cybersecurity independently',
            'wait'       => 'Wait for another free opportunity',
            'move_on'    => 'Move on to something else',
        ],
        'motivation_interest_areas' => [
            'pentest'     => 'Ethical Hacking / Penetration Testing',
            'forensics'   => 'Digital Forensics',
            'network'     => 'Network Security',
            'investigate' => 'Cybercrime Investigation',
            'compliance'  => 'Compliance & Governance',
            'other'       => 'Other (please specify)',
        ],
        'skill_levels' => [
            'beginner'     => "I'm a complete beginner",
            'somewhat'     => "I'm somewhat tech-inclined",
            'intermediate' => "I'm intermediate",
            'advanced'     => "I'm advanced",
        ],
        'skill_project_responses' => [
            'panic'     => 'I would panic — I need guidance from scratch',
            'seek_help' => 'I would try to figure it out with help',
            'independent' => 'I would research and get it done confidently',
        ],
        'skill_familiarity' => [
            'never'       => 'Never heard of it before',
            'read'        => "I've read about it",
            'short_course'=> "I've taken a short course",
            'labs'        => "I've done some practical labs",
        ],
        'discovery_channels' => [
            'social'   => 'Social media (Instagram / Twitter / LinkedIn)',
            'referral' => 'Referral / Friend',
            'whatsapp' => 'WhatsApp group',
            'other'    => 'Other',
        ],
    ],
];
