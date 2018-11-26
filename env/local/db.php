<?php

return [
    'master' => [
        'dbname' => '127.0.0.1',
        'host' => '3306',
        'user' => 'root',
        'password' => ''
    ],
    'slave_list' => [
        [
            'dbname' => '127.0.0.1',
            'host' => '3306',
            'user' => 'root',
            'password' => ''
        ]
    ],
    'time_out' => 3,
    'charset' => 'utf8mb4'
];