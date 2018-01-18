<?php

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