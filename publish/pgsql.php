<?php
return [
    'default' => [
        'username' => env('PGSQL_USERNAME', ''),
        'password' => env('PGSQL_PASSWORD', ''),
        'host' => env('PGSQL_HOST', '127.0.0.1'),
        'port' => env('PGSQL_PORT', 5432),
        'database' => env('PGSQL_DB', 'postgres'),
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 100,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => (float)env('PGSQL_MAX_IDLE_TIME', 60),
        ],
    ],
];
