<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use PDO;

final class CategoryRepository
{
    public function __construct(private readonly PDO $pdo) {}

    /**
     * Возвращает все категории в алфавитном порядке.
     * Используется в навигации и выпадающих списках.
     *
     * @return Category[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categories ORDER BY name');
        return array_map(Category::fromArray(...), $stmt->fetchAll());
    }

    /**
     * Создаёт новую категорию и возвращает её ID.
     *
     * @return int ID новой записи
     */
    public function insert(string $name, string $description): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO categories (name, description) VALUES (:name, :description)'
        );
        $stmt->execute([':name' => $name, ':description' => $description]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Обновляет название и описание категории.
     * created_at и updated_at не трогает — MySQL обновит updated_at автоматически.
     */
    public function update(int $id, string $name, string $description): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE categories SET name = :name, description = :description WHERE id = :id'
        );
        $stmt->execute([':name' => $name, ':description' => $description, ':id' => $id]);
    }

    /**
     * Возвращает массив ID категорий, к которым относится статья.
     * Используется для предзаполнения чекбоксов в форме редактирования.
     *
     * @return int[]
     */
    public function findIdsByPostId(int $postId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT category_id FROM post_categories WHERE post_id = :post_id'
        );
        $stmt->execute([':post_id' => $postId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * Возвращает только категории, у которых есть хотя бы одна статья.
     * EXISTS быстрее чем JOIN + GROUP BY + HAVING, когда нужен лишь факт наличия.
     *
     * @return Category[]
     */
    public function findAllWithPosts(): array
    {
        $stmt = $this->pdo->query(
            'SELECT c.*
               FROM categories c
              WHERE EXISTS (
                  SELECT 1 FROM post_categories pc WHERE pc.category_id = c.id
              )
              ORDER BY c.name'
        );
        return array_map(Category::fromArray(...), $stmt->fetchAll());
    }

    /**
     * Находит категорию по первичному ключу.
     *
     * @return Category|null null — если категория не существует
     */
    public function findById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM categories WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();
        return $row ? Category::fromArray($row) : null;
    }

    /**
     * Возвращает все категории конкретной статьи через таблицу-связку.
     * Используется для вывода тегов на странице статьи.
     *
     * @return Category[]
     */
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
