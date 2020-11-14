<?php
return [
    'payments' => [
        'log' => env('ELUTRADE_PAYMENTS_LOG', true),
        'api' => [
            'paypal' => [
                'environment' => env('PAYPAL_ENV', 'sandbox'),
                'client_id' => env('PAYPAL_CLIENT_ID'),
                'secret' => env('PAYPAL_SECRET')
            ]
        ]
    ]
];
