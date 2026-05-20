<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;

class ArticleController extends BaseController
{
    public function show(Request $request, array $params): void
    {
        $id     = (int) $params['id'];
        $result = $this->postService->getPostDetail($id);

        if ($result === null) {
            throw new HttpException(404);
        }

        $this->view->render('article/show', [
            'post'       => $result['post'],
            'similar'    => $result['similar'],
            'categories' => $this->categoryService->getAllCategories(),
        ]);
    }
}
