<?php

declare(strict_types=1);

return [
    'env'   => $_ENV['APP_ENV']   ?? 'local',
    'debug' => (bool) ($_ENV['APP_DEBUG'] ?? true),
    'url'   => $_ENV['APP_URL']   ?? 'http://localhost',

    'views_dir'   => dirname(__DIR__) . '/app/Views',
    'cache_dir'   => dirname(__DIR__) . '/storage/smarty/cache',
    'compile_dir' => dirname(__DIR__) . '/storage/smarty/compiled',
];
