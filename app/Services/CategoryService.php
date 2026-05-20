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

    /**
     * Все категории в алфавитном порядке.
     * Используется для навигационного меню на каждой странице сайта.
     *
     * @return Category[]
     */
    public function getAllCategories(): array
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * Тонкий прокси к репозиторию — без побочных эффектов.
     */
    public function findById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    /**
     * ID категорий, к которым принадлежит статья.
     * Используется для предзаполнения чекбоксов в форме редактирования статьи.
     *
     * @return int[]
     */
    public function getCategoryIdsByPostId(int $postId): array
    {
        return $this->categoryRepository->findIdsByPostId($postId);
    }

    /**
     * Создаёт категорию и возвращает её ID.
     */
    public function createCategory(string $name, string $description): int
    {
        return $this->categoryRepository->insert($name, $description);
    }

    /**
     * Обновляет название и описание категории.
     */
    public function updateCategory(int $id, string $name, string $description): void
    {
        $this->categoryRepository->update($id, $name, $description);
    }

    /**
     * Формирует секции главной страницы: каждая категория + 3 последних поста.
     *
     * Возвращает только категории, у которых есть статьи (пустые скрыты).
     * Выполняет N+1 запросов (по одному на категорию), что допустимо
     * для небольшого блога — категорий обычно меньше 20.
     *
     * @return array<array{category: Category, posts: Post[]}>
     */
    public function getCategoriesWithLatestPosts(): array
    {
        $categories = $this->categoryRepository->findAllWithPosts();

        return array_map(function (Category $category): array {
            $posts = $this->postRepository->findLatestByCategoryId($category->id, 3);
            return ['category' => $category, 'posts' => $posts];
        }, $categories);
    }

    /**
     * Возвращает категорию со списком статей и параметрами пагинации.
     *
     * @param  string $sort 'views' или 'date'
     * @return array{category: Category, posts: Post[], pagination: Pagination}|null
     *         null — если категория не найдена
     */
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
