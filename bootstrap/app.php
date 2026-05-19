<?php

declare(strict_types=1);

use App\Controllers\ArticleController;
use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Core\Application;
use App\Core\Database;
use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Services\CategoryService;
use App\Services\PostService;

// ── Load .env ────────────────────────────────────────────────────────────────
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// ── Wire dependencies ────────────────────────────────────────────────────────
$pdo  = Database::getInstance();
$view = new View();

$postRepo     = new PostRepository($pdo);
$categoryRepo = new CategoryRepository($pdo);

$postService     = new PostService($postRepo, $categoryRepo);
$categoryService = new CategoryService($categoryRepo, $postRepo);

$home     = new HomeController($postService, $categoryService, $view);
$category = new CategoryController($categoryService, $view);
$article  = new ArticleController($postService, $categoryService, $view);

// ── Build application & register routes ─────────────────────────────────────
$app = new Application();

require BASE_PATH . '/routes/web.php';

return $app;
