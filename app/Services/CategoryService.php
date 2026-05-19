<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Pagination;
use App\Models\Category;
use App\Models\Post;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;

final class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly PostRepository     $postRepository,
    ) {}

    /** @return Category[] */
    public function getAllCategories(): array
    {
        return $this->categoryRepository->findAll();
    }

    /** @return array{category: Category, posts: Post[], pagination: Pagination}|null */
    public function getCategoryWithPosts(int $id, string $sort = 'date', int $page = 1): ?array
    {
        $category = $this->categoryRepository->findById($id);

        if ($category === null) {
            return null;
        }

        $perPage    = 10;
        $total      = $this->postRepository->countByCategoryId($id);
        $pagination = new Pagination($total, $perPage, $page);

        $posts = $this->postRepository->findByCategoryId($id, $sort, $perPage, $pagination->offset);

        return ['category' => $category, 'posts' => $posts, 'pagination' => $pagination];
    }
}
