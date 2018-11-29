<?php

return [
    'master' => [
        'dbname' => 'test',
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => '',
        'time_out' => 3,
        'charset' => 'utf8mb4'
    ],
    'slave_list' => [
        [
            'dbname' => 'test',
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'root',
            'password' => '',
            'time_out' => 3,
            'charset' => 'utf8mb4'
        ]
    ]
];