<?php

declare(strict_types=1);

return [
    'db' => [
        'host'     => $_ENV['DB_HOST']     ?? '127.0.0.1',
        'port'     => $_ENV['DB_PORT']     ?? '3306',
        'database' => $_ENV['DB_NAME']     ?? 'blog',
        'username' => $_ENV['DB_USER']     ?? 'root',
        'password' => $_ENV['DB_PASS']     ?? '',
        'charset'  => $_ENV['DB_CHARSET']  ?? 'utf8mb4',
    ],
    'smarty' => [
        'template_dir' => dirname(__DIR__) . '/app/Views',
        'compile_dir'  => dirname(__DIR__) . '/storage/smarty/compiled',
        'cache_dir'    => dirname(__DIR__) . '/storage/smarty/cache',
    ],
    'pagination' => [
        'per_page' => 10,
    ],
];
