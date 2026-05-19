<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Services\CategoryService;

final class CategoryController
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly View            $view,
    ) {}

    public function show(Request $request, array $params): void
    {
        $id   = (int) $params['id'];
        $sort = $request->query('sort', 'date');
        $page = (int) $request->query('page', 1);

        $result = $this->categoryService->getCategoryWithPosts($id, $sort, $page);

        if ($result === null) {
            http_response_code(404);
            $this->view->render('errors/404');
            return;
        }

        $this->view->render('category/index', [
            'category'   => $result['category'],
            'posts'      => $result['posts'],
            'pagination' => $result['pagination'],
            'categories' => $this->categoryService->getAllCategories(),
            'sort'       => $sort,
        ]);
    }
}
