<?php

declare(strict_types = 1);

return [
    'migration_dirs' => [
        'migrations' => __DIR__ . '/../../migrations',
    ],
    'environments' => [
        'local' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'port' => 3306, 
            'username' => 'root',
            'password' => '',
            'db_name' => 'nazdy',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci', 
        ],
        'production' => [
            'adapter' => 'mysql',
            'host' => 'production_host',
            'port' => 3306, 
            'username' => 'root',
            'password' => '',
            'db_name' => 'nazdy',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci', 
        ],
    ],
    'default_environment' => 'local',
    'log_table_name' => 'migrations_log',
];