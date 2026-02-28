<?php

return [
    'default' => env('PAYMENTS_TYPE', 'cash-in-hand'),

    'types' => [
        'cash-in-hand' => [
            'driver' => 'coffline',
            'authorized' => 'order_under_delivery',
        ],
        'fib' => [
            'driver' => 'fib',
            'authorized' => 'order_requested',
            'captured' => 'order_requested'
        ],
    ],
];
