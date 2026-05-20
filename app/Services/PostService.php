<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Pagination;
use App\Models\Post;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;

final class PostService
{
    public function __construct(
        private readonly PostRepository     $postRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {}

    /**
     * Тонкий прокси к репозиторию — без побочных эффектов.
     * Нужен контроллерам, которым требуется сырой Post без инкремента просмотров.
     */
    public function findById(int $id): ?Post
    {
        return $this->postRepository->findById($id);
    }

    /**
     * Возвращает все статьи для таблицы в админке.
     * Лимит 1000 — достаточно для небольшого блога; при росте заменить на пагинацию.
     *
     * @return Post[]
     */
    public function getAllPosts(): array
    {
        return $this->postRepository->findAll('date', 1000, 0);
    }

    /**
     * Обновляет поля статьи и пересинхронизирует категории.
     *
     * Категории заменяются полностью: сначала удаляются все старые связи,
     * затем вставляются новые. Это проще и надёжнее, чем вычислять diff.
     *
     * @param int[]   $categoryIds новый набор категорий
     */
    public function updatePost(
        int     $id,
        string  $title,
        string  $description,
        string  $content,
        ?string $image,
        array   $categoryIds,
    ): void {
        $this->postRepository->update($id, $title, $description, $content, $image);
        $this->postRepository->clearCategories($id);
        $this->postRepository->syncCategories($id, $categoryIds);
    }

    /**
     * Создаёт статью и привязывает к ней категории.
     *
     * @param  int[]  $categoryIds
     * @return int    ID новой статьи
     */
    public function createPost(
        string  $title,
        string  $description,
        string  $content,
        ?string $image,
        array   $categoryIds,
    ): int {
        $postId = $this->postRepository->insert($title, $description, $content, $image);
        $this->postRepository->syncCategories($postId, $categoryIds);

        return $postId;
    }

    /**
     * Возвращает постраничный список статей для публичного сайта.
     *
     * @param  string $sort 'views' или 'date'
     * @return array{posts: Post[], pagination: Pagination}
     */
    public function getHomePagePosts(string $sort = 'date', int $page = 1): array
    {
        $perPage    = 10;
        $total      = $this->postRepository->countAll();
        $pagination = new Pagination($total, $perPage, $page);

        $posts = $this->postRepository->findAll($sort, $perPage, $pagination->offset);

        return ['posts' => $posts, 'pagination' => $pagination];
    }

    /**
     * Возвращает полные данные статьи для публичной страницы.
     *
     * Побочный эффект: инкрементирует views_count в БД при каждом вызове.
     * viewsCount в возвращаемом объекте уже отражает новое значение (+1),
     * чтобы пользователь видел актуальное число без повторного SELECT.
     *
     * @return array{post: Post, similar: Post[]}|null null — если статья не найдена
     */
    public function getPostDetail(int $id): ?array
    {
        $post = $this->postRepository->findById($id);

        if ($post === null) {
            return null;
        }

        $this->postRepository->incrementViews($id);

        $categories  = $this->categoryRepository->findByPostId($id);
        $categoryIds = array_map(fn($c) => $c->id, $categories);
        $similar     = $this->postRepository->findSimilar($id, $categoryIds, 3);

        $post = new Post(
            id:          $post->id,
            title:       $post->title,
            description: $post->description,
            content:     $post->content,
            viewsCount:  $post->viewsCount + 1,
            createdAt:   $post->createdAt,
            updatedAt:   $post->updatedAt,
            image:       $post->image,
            categories:  $categories,
        );

        return ['post' => $post, 'similar' => $similar];
    }
}
