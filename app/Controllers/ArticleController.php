<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;

class ArticleController extends BaseController
{
    public function show(Request $request, array $params): void
    {
        $id   = (int) $params['id'];
        $post = $this->postService->getPostDetail($id);

        if ($post === null) {
            throw new HttpException(404);
        }

        $this->view->render('article/show', [
            'post'       => $post,
            'categories' => $this->categoryService->getAllCategories(),
        ]);
    }
}
