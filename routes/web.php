<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\ArticleController;
use App\Controllers\CategoryController;
use App\Controllers\HomeController;

/** @var \App\Core\Router $router */

$router->get('/',                    [HomeController::class,    'index']);
$router->get('/category/{id:\d+}',   [CategoryController::class,'show']);
$router->get('/post/{id:\d+}',       [ArticleController::class, 'show']);

// Admin — auth
$router->get('/admin/login',          [AdminController::class, 'loginForm']);
$router->post('/admin/login',         [AdminController::class, 'login']);
$router->get('/admin/logout',         [AdminController::class, 'logout']);

// Admin — posts
$router->get('/admin',                         [AdminController::class, 'dashboard']);
$router->get('/admin/posts/create',            [AdminController::class, 'createForm']);
$router->post('/admin/posts/create',           [AdminController::class, 'create']);
$router->get('/admin/posts/{id:\d+}/edit',     [AdminController::class, 'editPostForm']);
$router->post('/admin/posts/{id:\d+}/edit',    [AdminController::class, 'updatePost']);

// Admin — categories
$router->get('/admin/categories',              [AdminController::class, 'categoriesList']);
$router->get('/admin/categories/create',       [AdminController::class, 'createCategoryForm']);
$router->post('/admin/categories/create',      [AdminController::class, 'createCategory']);
$router->get('/admin/categories/{id:\d+}/edit',  [AdminController::class, 'editCategoryForm']);
$router->post('/admin/categories/{id:\d+}/edit', [AdminController::class, 'updateCategory']);
