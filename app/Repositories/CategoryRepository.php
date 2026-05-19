<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use PDO;

final class CategoryRepository
{
    public function __construct(private readonly PDO $pdo) {}

    /** @return Category[] */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categories ORDER BY name');
        return array_map(Category::fromArray(...), $stmt->fetchAll());
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM categories WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();
        return $row ? Category::fromArray($row) : null;
    }

    /** @return Category[] */
    public function findByPostId(int $postId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.*
               FROM categories c
               JOIN post_categories pc ON pc.category_id = c.id
              WHERE pc.post_id = :post_id'
        );
        $stmt->execute([':post_id' => $postId]);

        return array_map(Category::fromArray(...), $stmt->fetchAll());
    }
}
