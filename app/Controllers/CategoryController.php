<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;

class CategoryController extends BaseController
{
    public function show(Request $request, array $params): void
    {
        $id   = (int) $params['id'];
        $sort = $request->getQuery('sort', 'date');
        $page = $request->getInt('page', 1);

        $result = $this->categoryService->getCategoryWithPosts($id, $sort, $page);

        if ($result === null) {
            throw new HttpException(404);
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
