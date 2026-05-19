<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Services\CategoryService;
use App\Services\PostService;

final class ArticleController
{
    public function __construct(
        private readonly PostService     $postService,
        private readonly CategoryService $categoryService,
        private readonly View            $view,
    ) {}

    public function show(Request $request, array $params): void
    {
        $id   = (int) $params['id'];
        $post = $this->postService->getPostDetail($id);

        if ($post === null) {
            http_response_code(404);
            $this->view->render('errors/404');
            return;
        }

        $this->view->render('article/show', [
            'post'       => $post,
            'categories' => $this->categoryService->getAllCategories(),
        ]);
    }
}
