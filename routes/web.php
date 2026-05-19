<?php

declare(strict_types=1);

/**
 * @var \App\Core\Application     $app
 * @var \App\Controllers\HomeController     $home
 * @var \App\Controllers\CategoryController $category
 * @var \App\Controllers\ArticleController  $article
 */

use App\Core\Request;

$app->router->get('/', fn(Request $r) => $home->index($r));

$app->router->get('/category/{id:\d+}',           fn(Request $r, array $p) => $category->show($r, $p));
$app->router->get('/category/{id:\d+}/{page:\d+}', fn(Request $r, array $p) => $category->show($r, $p));

$app->router->get('/post/{id:\d+}',          fn(Request $r, array $p) => $article->show($r, $p));
$app->router->get('/post/{id:\d+}/{slug}',   fn(Request $r, array $p) => $article->show($r, $p));
