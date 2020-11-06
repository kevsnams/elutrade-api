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
                'id' => 'hook_u1tGny6Ludz1d6w5Mdj2YMfk',
                'url' => 'https://api.elutrade.com/paymongo/webhooks/source-chargeable'
            ]
        ]
    ]
];
