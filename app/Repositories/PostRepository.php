<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Post;
use PDO;

final class PostRepository
{
    public function __construct(private readonly PDO $pdo) {}

    /**
     * Возвращает постраничный список статей.
     *
     * Сортировка задаётся строкой 'views' или любым другим значением (= по дате).
     * ORDER BY строится из белого списка, поэтому SQL-инъекция через $sort невозможна.
     *
     * @param  string  $sort   'views' — по просмотрам, иначе — по дате
     * @param  int     $limit  кол-во записей (LIMIT)
     * @param  int     $offset смещение (OFFSET), вычисляется Pagination
     * @return Post[]
     */
    public function findAll(string $sort = 'date', int $limit = 10, int $offset = 0): array
    {
        $orderBy = $sort === 'views' ? 'views_count DESC' : 'created_at DESC';

        $stmt = $this->pdo->prepare(
            "SELECT * FROM posts ORDER BY {$orderBy} LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(Post::fromArray(...), $stmt->fetchAll());
    }

    /**
     * Находит статью по первичному ключу.
     *
     * @return Post|null null — если статья не существует
     */
    public function findById(int $id): ?Post
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM posts WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();
        return $row ? Post::fromArray($row) : null;
    }

    /**
     * Возвращает статьи конкретной категории с пагинацией и сортировкой.
     *
     * @param  int    $categoryId
     * @param  string $sort       'views' или 'date'
     * @param  int    $limit
     * @param  int    $offset
     * @return Post[]
     */
    public function findByCategoryId(int $categoryId, string $sort = 'date', int $limit = 10, int $offset = 0): array
    {
        $orderBy = $sort === 'views' ? 'p.views_count DESC' : 'p.created_at DESC';

        $stmt = $this->pdo->prepare(
            "SELECT p.*
               FROM posts p
               JOIN post_categories pc ON pc.post_id = p.id
              WHERE pc.category_id = :category_id
              ORDER BY {$orderBy}
              LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',       $limit,      PDO::PARAM_INT);
        $stmt->bindValue(':offset',      $offset,     PDO::PARAM_INT);
        $stmt->execute();

        return array_map(Post::fromArray(...), $stmt->fetchAll());
    }

    /**
     * Общее кол-во статей — нужно Pagination для расчёта totalPages.
     */
    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
    }

    /**
     * Кол-во статей в категории — нужно Pagination на странице категории.
     */
    public function countByCategoryId(int $categoryId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM post_categories WHERE category_id = :id'
        );
        $stmt->execute([':id' => $categoryId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Атомарно увеличивает счётчик просмотров на 1.
     * Вызывается при каждом открытии страницы статьи.
     */
    public function incrementViews(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE posts SET views_count = views_count + 1 WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
    }

    /**
     * Обновляет поля статьи (без изменения created_at и views_count).
     * updated_at обновляется автоматически триггером ON UPDATE в MySQL.
     */
    public function update(
        int     $id,
        string  $title,
        string  $description,
        string  $content,
        ?string $image,
    ): void {
        $stmt = $this->pdo->prepare(
            'UPDATE posts
                SET title = :title, description = :description,
                    content = :content, image = :image
              WHERE id = :id'
        );
        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':content'     => $content,
            ':image'       => $image,
            ':id'          => $id,
        ]);
    }

    /**
     * Удаляет все связи статьи с категориями.
     * Вызывается перед syncCategories() при редактировании, чтобы
     * заменить набор категорий целиком (delete + re-insert).
     */
    public function clearCategories(int $postId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM post_categories WHERE post_id = :post_id'
        );
        $stmt->execute([':post_id' => $postId]);
    }

    /**
     * Создаёт новую статью и возвращает её ID.
     *
     * @return int ID новой записи
     */
    public function insert(
        string  $title,
        string  $description,
        string  $content,
        ?string $image,
    ): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO posts (title, description, content, image, views_count, created_at)
             VALUES (:title, :description, :content, :image, 0, NOW())'
        );
        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':content'     => $content,
            ':image'       => $image,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Привязывает категории к статье.
     *
     * INSERT IGNORE безопасен при повторном вызове — дубликаты молча игнорируются
     * благодаря PRIMARY KEY (post_id, category_id) в таблице post_categories.
     *
     * @param int[] $categoryIds
     */
    public function syncCategories(int $postId, array $categoryIds): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO post_categories (post_id, category_id) VALUES (:post_id, :category_id)'
        );

        foreach ($categoryIds as $categoryId) {
            $stmt->execute([':post_id' => $postId, ':category_id' => (int) $categoryId]);
        }
    }

    /**
     * Возвращает $limit последних статей категории — для секций главной страницы.
     *
     * @return Post[]
     */
    public function findLatestByCategoryId(int $categoryId, int $limit = 3): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*
               FROM posts p
               JOIN post_categories pc ON pc.post_id = p.id
              WHERE pc.category_id = :category_id
              ORDER BY p.created_at DESC
              LIMIT :limit'
        );
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',       $limit,      PDO::PARAM_INT);
        $stmt->execute();

        return array_map(Post::fromArray(...), $stmt->fetchAll());
    }

    /**
     * Возвращает похожие статьи из тех же категорий.
     *
     * DISTINCT нужен, потому что статья может лежать в нескольких совпадающих
     * категориях и без него появятся дубликаты строк.
     *
     * Позиционные параметры (?) используются вместо именованных, потому что
     * IN() требует динамического кол-ва плейсхолдеров.
     *
     * @param  int[]  $categoryIds категории текущей статьи
     * @return Post[]
     */
    public function findSimilar(int $postId, array $categoryIds, int $limit = 3): array
    {
        if (empty($categoryIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $stmt = $this->pdo->prepare(
            "SELECT DISTINCT p.*
               FROM posts p
               JOIN post_categories pc ON pc.post_id = p.id
              WHERE pc.category_id IN ({$placeholders})
                AND p.id != ?
              ORDER BY p.created_at DESC
              LIMIT ?"
        );

        $params   = array_values($categoryIds);
        $params[] = $postId;
        $params[] = $limit;
        $stmt->execute($params);

        return array_map(Post::fromArray(...), $stmt->fetchAll());
    }
}
