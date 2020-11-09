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
                'id' => 'hook_QFJVfN5W4bo42X1X2Rk8aZuu',
                'url' => 'https://api.elutrade.com/api/v1/paymongo/webhooks/source-chargeable'
            ]
        ]
    ]
];
