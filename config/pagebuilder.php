<?php

return [
    'globals' => [
        'header_block_type' => null,
        'footer_block_type' => null,
    ],

    'kinds' => [
        'generic' => [
            'label' => 'Generic Page',
            'required' => [],
            'one_of_groups' => [],
            'allowed' => null,
        ],

        'bootcamp' => [
            'label' => 'Bootcamp Page',
            'required' => ['hero','program_overview','pricing_recap','faq','closing_cta'],
            'one_of_groups' => [
                ['foundations', 'curriculum'],
            ],
            'allowed' => null,
        ],
    ],

    'block_rules' => [
        'hero' => [
            'title' => ['required','string','min:3'],
        ],
        'program_overview' => [
            'items' => ['required','array','min:1'],
            'items.*.title' => ['nullable','string'],
            'items.*.text'  => ['nullable','string'],
        ],
        'foundations' => [
            'bullets' => ['required','array','min:3'],
        ],
        'curriculum' => [
            'phases' => ['required','array','min:1'],
        ],
        'pricing_recap' => [
            'plans' => ['required','array','min:1'],
            'plans.*.name' => ['required','string'],
            'plans.*.price'=> ['required','string'],
        ],
        'faq' => [
            'items' => ['required','array','min:1'],
            'items.*.q' => ['required','string'],
            'items.*.a' => ['required','string'],
        ],
        'closing_cta' => [
            'title' => ['required','string','min:3'],
        ],
    ],
];