<?php

if (app()->environment('local')) {
    return [
        'default' => 'mongodb',
        'connections' => [
            'mongodb' => [
                'driver'   => 'mongodb',
                'host'     => env('DB_HOST', 'localhost'),
                'port'     => env('DB_PORT', 27017),
                'database' => env('DB_DATABASE'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'options'  => [
                    'database' => 'admin' // sets the authentication database required by mongo 3
                ]
            ],
        ]
    ];
}

return [
    'default' => 'mongodb',
    'connections' => [
        'mongodb' => [
            'driver'   => 'mongodb',
            'database' => env('DB_DATABASE'),
            'dsn'=> env('DB_DNS'),
            'options'  => [
                'database' => 'admin' // sets the authentication database required by mongo 3
            ]
        ],
    ]
];