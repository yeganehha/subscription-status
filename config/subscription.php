<?php

use App\Enums\StatusEnum;

return [
    'active_days' => [
        'friday',
        6,
        'Friday',
        'fri'
    ],

    'email_when' => [
        'from' => StatusEnum::Active,
        'to' => StatusEnum::Expired,
    ],

    'email' => [
        'subject' => 'Pending apps!',
        'to' =>[
            'admin@parspack.com'
        ]
    ]
];
