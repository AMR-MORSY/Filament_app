<?php

return [

    // Minimum minutes from now before a same-day slot can be booked
    'booking_lead_minutes' => (int) env('BOOKING_LEAD_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | Phone dial codes
    |--------------------------------------------------------------------------
    |
    | Keyed by ISO 3166-1 alpha-2 region, because libphonenumber needs a region
    | to interpret a national number — the trunk prefix differs by country (Egypt
    | drops its leading 0, Italy keeps its own). Dial codes are not listed here:
    | PhoneNumber::dialCode() reads them from libphonenumber, so they cannot drift.
    |
    | Numbers are stored as a single E.164 string, so the column stays searchable.
    |
    */
    'default_country' => 'EG',

    'countries' => [
        'EG' => 'Egy',
        'SA' => 'KSA',
        'AE' => 'UAE',
        'KW' => 'Kuw',
        'QA' => 'Qat',
        'BH' => 'Bah',
        'OM' => 'Oma',
        'JO' => 'Jor',
        'LB' => 'Leb',
        'SD' => 'Sud',
        'LY' => 'Lib',
        'MA' => 'Mor',
        'TN' => 'Tun',
        'DZ' => 'Alg',
        'TR' => 'Tur',
        'GB' => 'UK',
        'US' => 'USA',
        'DE' => 'Ger',
        'FR' => 'Fra',
        'IT' => 'Ita',
        'IN' => 'Ind',
    ],

    /*
    |--------------------------------------------------------------------------
    | Patient testimonials
    |--------------------------------------------------------------------------
    |
    | PLACEHOLDER COPY — replace before going live. These are written to show
    | the layout, not to be published. Publishing invented patient reviews is
    | misleading and, in most jurisdictions, unlawful advertising. Use real
    | quotes with the patient's written consent, and keep names abbreviated.
    |
    */
    'testimonials' => [
        [
            'quote' => 'I booked at eleven at night and had an appointment the next morning. '
                . 'No phone call, no waiting for someone to ring me back.',
            'name' => 'Nour A.',
            'context' => 'Booked for a parent',
            'rating' => 5,
        ],
        [
            'quote' => 'My mother is 74 and she did it herself on her phone. '
                . 'She picked the time she wanted and that was the end of it.',
            'name' => 'Karim H.',
            'context' => 'Cardiology',
            'rating' => 5,
        ],
        [
            'quote' => 'I had to move my appointment twice. Both times it took seconds, '
                . 'and nobody made me feel like I was being a nuisance.',
            'name' => 'Salma E.',
            'context' => 'Orthopedics',
            'rating' => 5,
        ],
    ],

];
