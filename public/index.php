<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load .env
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

use App\Controllers\ArticleController;
use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Core\Database;
use App\Core\Request;
use App\Core\Router;
use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Services\CategoryService;
use App\Services\PostService;

// Bootstrap
$pdo  = Database::getInstance();
$view = new View();

$postRepo     = new PostRepository($pdo);
$categoryRepo = new CategoryRepository($pdo);

$postService     = new PostService($postRepo, $categoryRepo);
$categoryService = new CategoryService($categoryRepo, $postRepo);

$homeController     = new HomeController($postService, $categoryService, $view);
$categoryController = new CategoryController($categoryService, $view);
$articleController  = new ArticleController($postService, $categoryService, $view);

// Routes
$router  = new Router();
$request = new Request();

$router->get('/', fn(Request $req) => $homeController->index($req));
$router->get('/category/{id}', fn(Request $req, array $p) => $categoryController->show($req, $p));
$router->get('/post/{id}',     fn(Request $req, array $p) => $articleController->show($req, $p));

$router->dispatch($request);
