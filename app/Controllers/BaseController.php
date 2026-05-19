<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Services\CategoryService;
use App\Services\PostService;

abstract class BaseController
{
    protected View            $view;
    protected PostService     $postService;
    protected CategoryService $categoryService;
    protected int             $perPage;

    public function __construct(array $config)
    {
        $database           = new Database($config['db']);
        $postRepository     = new PostRepository($database->getPdo());
        $categoryRepository = new CategoryRepository($database->getPdo());

        $this->postService     = new PostService($postRepository, $categoryRepository);
        $this->categoryService = new CategoryService($categoryRepository, $postRepository);
        $this->view            = new View($config);
        $this->perPage         = (int) ($config['pagination']['per_page'] ?? 10);
    }
}
