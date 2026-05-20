<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Post;
use PDO;

final class PostRepository
{
    public function __construct(private readonly PDO $pdo) {}

    /** @return Post[] */
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

    public function findById(int $id): ?Post
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM posts WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();
        return $row ? Post::fromArray($row) : null;
    }

    /** @return Post[] */
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

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
    }

    public function countByCategoryId(int $categoryId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM post_categories WHERE category_id = :id'
        );
        $stmt->execute([':id' => $categoryId]);
        return (int) $stmt->fetchColumn();
    }

    public function incrementViews(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE posts SET views_count = views_count + 1 WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
    }

    /** @return Post[] */
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
     * @param  int[]  $categoryIds
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
