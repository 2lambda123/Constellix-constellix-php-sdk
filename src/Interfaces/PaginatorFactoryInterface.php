<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces;

/**
 * A factory for creating paginated collections of items.
 * @package Constellix\Client\Interfaces
 */
interface PaginatorFactoryInterface
{
    /**
     * Returns a pagination object based on the supplied parameters.
     * @param array<mixed> $items
     * @param int $totalItems
     * @param int $perPage
     * @param int $currentPage
     * @return mixed
     */
    public function paginate(array $items, int $totalItems, int $perPage, int $currentPage = 1);
}
