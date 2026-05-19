<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;

class HomeController extends BaseController
{
    public function index(Request $request, array $params): void
    {
        $sort = $request->getQuery('sort', 'date');
        $page = $request->getInt('page', 1);

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
