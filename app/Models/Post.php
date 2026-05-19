<?php

declare(strict_types=1);

namespace App\Models;

final class Post
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $title,
        public readonly string  $description,
        public readonly string  $content,
        public readonly int     $viewsCount,
        public readonly string  $createdAt,
        public readonly ?string $image = null,
        /** @var Category[] */
        public readonly array   $categories = [],
    ) {}

    public static function fromArray(array $row): self
    {
        return new self(
            id:          (int) $row['id'],
            title:       $row['title'],
            description: $row['description'],
            content:     $row['content'],
            viewsCount:  (int) $row['views_count'],
            createdAt:   $row['created_at'],
            image:       $row['image'] ?? null,
        );
    }
}
