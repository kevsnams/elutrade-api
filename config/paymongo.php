<?php

return [
    'keys' => [
        'public' => env('PAYMONGO_PUBLIC_KEY'),
        'private' => env('PAYMONGO_SECRET_KEY')
    ],

    'url' => [
        'success' => env('PAYMONGO_URL_SUCCESS', 'http://TBD'),
        'failed' => env('PAYMONGO_URL_FAIL', 'http://TBD')
    ],

    'webhooks' => [
        'source' => [
            'chargeable' => [
                // TODO get from env
                'id' => env('PAYMONGO_WEBHOOKS_SOURCE_CHARGEABLE_ID'),
                'url' => env('PAYMONGO_WEBHOOKS_SOURCE_CHARGEABLE_URL')
            ]
        ]
    ]
];
