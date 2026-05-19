<?php

declare(strict_types=1);

namespace App\Core;

final class Pagination
{
    public readonly int $currentPage;
    public readonly int $totalPages;
    public readonly int $perPage;
    public readonly int $offset;

    public function __construct(int $totalItems, int $perPage = 10, int $currentPage = 1)
    {
        $this->perPage     = max(1, $perPage);
        $this->totalPages  = (int) ceil($totalItems / $this->perPage);
        $this->currentPage = max(1, min($currentPage, $this->totalPages ?: 1));
        $this->offset      = ($this->currentPage - 1) * $this->perPage;
    }

    public function hasPrev(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages;
    }
}
