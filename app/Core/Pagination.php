<?php

declare(strict_types=1);

namespace App\Core;

final class Pagination
{
    public readonly int $currentPage;
    public readonly int $totalPages;
    public readonly int $perPage;
    public readonly int $offset;

    /**
     * Вычисляет параметры пагинации.
     *
     * currentPage зажат в [1, totalPages], чтобы невалидный номер страницы
     * из URL не ломал OFFSET в SQL. При пустой выборке totalPages = 0, но
     * currentPage всё равно будет 1 (защита от деления на ноль).
     *
     * @param int $totalItems  общее кол-во записей
     * @param int $perPage     записей на страницу
     * @param int $currentPage запрошенная страница из URL
     */
    public function __construct(int $totalItems, int $perPage = 10, int $currentPage = 1)
    {
        $this->perPage     = max(1, $perPage);
        $this->totalPages  = (int) ceil($totalItems / $this->perPage);
        $this->currentPage = max(1, min($currentPage, $this->totalPages ?: 1));
        $this->offset      = ($this->currentPage - 1) * $this->perPage;
    }

    /**
     * Есть ли страница до текущей.
     */
    public function hasPrev(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Есть ли страница после текущей.
     */
    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages;
    }
}
