<?php

return [
    'category_caps' => [
        'commitment' => 25,
        'technical'  => 25,
        'education'  => 15,
        'motivation' => 20,
        'skills'     => 15,
        'attitude'   => 15,
        'personal'   => 10,
    ],

    'weights' => [
        [
            'path'     => 'commitment.hours_per_week',
            'category' => 'commitment',
            'label'    => 'Weekly availability',
            'values'   => [
                '20_plus' => 20,
                '15_19'   => 15,
                '10_14'   => 10,
                '6_9'     => 5,
                '0_5'     => 0,
            ],
        ],
        [
            'path'     => 'commitment.availability',
            'category' => 'commitment',
            'label'    => 'Availability schedule',
            'values'   => [
                'flexible'         => 12,
                'weekday_evenings' => 10,
                'weekends'         => 8,
                'limited'          => 2,
            ],
        ],
        [
            'path'     => 'technical.has_laptop',
            'category' => 'technical',
            'label'    => 'Laptop access',
            'values'   => [
                'yes' => 12,
                'no'  => 0,
            ],
        ],
        [
            'path'     => 'technical.internet',
            'category' => 'technical',
            'label'    => 'Internet quality',
            'values'   => [
                'excellent' => 10,
                'good'      => 7,
                'fair'      => 4,
                'poor'      => 0,
            ],
        ],
        [
            'path'     => 'education.highest_level',
            'category' => 'education',
            'label'    => 'Education level',
            'values'   => [
                'postgraduate' => 10,
                'bachelors'    => 8,
                'diploma'      => 6,
                'secondary'    => 4,
                'other'        => 2,
            ],
        ],
        [
            'path'     => 'education.currently_in_school',
            'category' => 'education',
            'label'    => 'Currently in school',
            'values'   => [
                'final_year' => 6,
                'yes'        => 4,
                'no'         => 2,
            ],
        ],
        [
            'path'     => 'personal.occupation_status',
            'category' => 'personal',
            'label'    => 'Occupation status',
            'values'   => [
                'unemployed'     => 8,
                'underemployed'  => 6,
                'student'        => 5,
                'employed'       => 3,
            ],
        ],
        [
            'path'     => 'motivation.previous_training',
            'category' => 'motivation',
            'label'    => 'Previous training',
            'values'   => [
                'yes' => 5,
                'no'  => 0,
            ],
        ],
        [
            'path'     => 'motivation.interest_area',
            'category' => 'motivation',
            'label'    => 'Interest area alignment',
            'values'   => [
                'security_operations' => 6,
                'governance_risk'     => 5,
                'cloud_security'      => 4,
                'other'               => 3,
            ],
        ],
        [
            'path'     => 'skills.level',
            'category' => 'skills',
            'label'    => 'Self-assessed skill level',
            'values'   => [
                'intermediate' => 10,
                'beginner'     => 7,
                'advanced'     => 8,
                'none'         => 3,
            ],
        ],
        [
            'path'     => 'skills.project_response',
            'category' => 'skills',
            'label'    => 'Project response',
            'values'   => [
                'comfortable' => 5,
                'unsure'      => 3,
                'no'          => 1,
            ],
        ],
        [
            'path'     => 'skills.familiarity',
            'category' => 'skills',
            'label'    => 'Tool familiarity',
            'values'   => [
                'hands_on' => 6,
                'studied'  => 4,
                'new'      => 2,
            ],
        ],
        [
            'path'     => 'attitude.participation',
            'category' => 'attitude',
            'label'    => 'Participation commitment',
            'values'   => [
                'yes' => 8,
                'no'  => 0,
            ],
        ],
        [
            'path'     => 'attitude.discovery_channel',
            'category' => 'attitude',
            'label'    => 'Discovery channel',
            'values'   => [
                'community_referral' => 5,
                'partner_program'    => 5,
                'social_media'       => 3,
                'other'              => 2,
            ],
        ],
        [
            'path'     => 'bonus.challenge_opt_in',
            'category' => 'attitude',
            'label'    => 'Challenge opt-in',
            'values'   => [
                'yes' => 4,
                'no'  => 0,
            ],
        ],
    ],

    'array_counts' => [
        [
            'path'      => 'technical.tools',
            'category'  => 'technical',
            'label'     => 'Familiar tools',
            'per_item'  => 2,
            'max_score' => 10,
        ],
    ],

    'text_lengths' => [
        [
            'path'     => 'motivation.reason',
            'category' => 'motivation',
            'label'    => 'Detailed motivation',
            'min'      => 800,
            'score'    => 10,
        ],
        [
            'path'     => 'motivation.future_plan',
            'category' => 'motivation',
            'label'    => 'Detailed future plan',
            'min'      => 600,
            'score'    => 8,
        ],
    ],

    'keyword_sets' => [
        [
            'path'          => 'motivation.reason',
            'category'      => 'motivation',
            'label'         => 'Motivation keywords',
            'keywords'      => ['mentor', 'community', 'career', 'impact', 'cybersecurity'],
            'score_per_hit' => 2,
            'max_score'     => 8,
        ],
        [
            'path'          => 'motivation.future_plan',
            'category'      => 'motivation',
            'label'         => 'Future plan keywords',
            'keywords'      => ['lead', 'teach', 'startup', 'mentor'],
            'score_per_hit' => 2,
            'max_score'     => 6,
        ],
    ],

    'penalties' => [
        [
            'path'     => 'technical.has_laptop',
            'category' => 'technical',
            'label'    => 'No laptop available',
            'matches'  => ['no'],
            'score'    => -10,
        ],
        [
            'path'     => 'technical.laptop_specs',
            'category' => 'technical',
            'label'    => 'Missing laptop specs',
            'blank_when' => ['technical.has_laptop' => 'yes'],
            'score'    => -5,
        ],
        [
            'path'     => 'technical.internet',
            'category' => 'technical',
            'label'    => 'Fair internet quality',
            'matches'  => ['fair'],
            'score'    => -2,
        ],
    ],

    'soft_rules' => [
        [
            'conditions' => [
                ['path' => 'technical.has_laptop', 'equals' => 'no'],
                ['path' => 'bonus.challenge_opt_in', 'equals' => 'yes'],
            ],
            'decision' => 'pending',
            'note'     => 'No laptop but willing to take on challenges',
        ],
        [
            'conditions' => [
                ['path' => 'commitment.availability', 'equals' => 'limited'],
                ['path' => 'commitment.hours_per_week', 'equals' => '6_9'],
            ],
            'decision' => 'pending',
            'note'     => 'Limited availability, needs manual review',
        ],
    ],

    'disqualifiers' => [
        [
            'path'    => 'attitude.commitment_agreement',
            'matches' => ['no'],
            'note'    => 'Commitment agreement declined',
        ],
        [
            'path'    => 'commitment.availability',
            'matches' => ['unavailable'],
            'note'    => 'Not available for sessions',
        ],
        [
            'path'    => 'technical.internet',
            'matches' => ['poor'],
            'note'    => 'Internet quality is poor',
        ],
    ],

    'auto_approve_threshold' => 70,
    'auto_reject_threshold'  => 30,
];
