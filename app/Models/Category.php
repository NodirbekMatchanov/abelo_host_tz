<?php

declare(strict_types=1);

namespace App\Models;

final class Category
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly ?string $description,
        public readonly string  $createdAt,
    ) {}

    public static function fromArray(array $row): self
    {
        return new self(
            id:          (int) $row['id'],
            name:        $row['name'],
            description: $row['description'] ?? null,
            createdAt:   $row['created_at'],
        );
    }
}
