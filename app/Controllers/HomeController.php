<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Services\CategoryService;
use App\Services\PostService;

final class HomeController
{
    public function __construct(
        private readonly PostService     $postService,
        private readonly CategoryService $categoryService,
        private readonly View            $view,
    ) {}

    public function index(Request $request): void
    {
        $sort = $request->query('sort', 'date');
        $page = (int) $request->query('page', 1);

        ['posts' => $posts, 'pagination' => $pagination] =
            $this->postService->getHomePagePosts($sort, $page);

        $this->view->render('home/index', [
            'posts'      => $posts,
            'pagination' => $pagination,
            'categories' => $this->categoryService->getAllCategories(),
            'sort'       => $sort,
        ]);
    }
}
