<?php

declare(strict_types=1);

use App\Controllers\ArticleController;
use App\Controllers\CategoryController;
use App\Controllers\HomeController;

/** @var \App\Core\Router $router */

$router->get('/',                   [HomeController::class,     'index']);
$router->get('/category/{id:\d+}',  [CategoryController::class, 'show']);
$router->get('/post/{id:\d+}',      [ArticleController::class,  'show']);
