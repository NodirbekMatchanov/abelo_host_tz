<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;

class HomeController extends BaseController
{
    public function index(Request $request, array $params): void
    {
        $this->view->render('home/index', [
            'sections'   => $this->categoryService->getCategoriesWithLatestPosts(),
            'categories' => $this->categoryService->getAllCategories(),
        ]);
    }
}
