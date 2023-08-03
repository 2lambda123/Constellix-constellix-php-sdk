<?php

declare(strict_types=1);

namespace Constellix\Client\Pagination;

use ArrayIterator;
use Traversable;

/**
 * Simple object paginator. Can be iterated over, accessed like an array and used in a similar manner to Illuminate's
 * LengthAwarePaginator.
 *
 * @implements \ArrayAccess<int,object>
 * @implements \IteratorAggregate<int>
 * @package Constellix\Pagination
 */
class Paginator implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var array<object>
     */
    protected array $items;
    protected int $totalItems;
    protected int $perPage;
    protected int $currentPage;
    protected int $lastPage;

    /**
     * Creates a new Paginator
     * @param array<object> $items
     * @param int $totalItems
     * @param int $perPage
     * @param int $currentPage
     */
    public function __construct(array $items, int $totalItems, int $perPage, int $currentPage = 1)
    {
        $this->items = $items;
        $this->totalItems = $totalItems;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        $this->lastPage = max((int)ceil($totalItems / $perPage), 1);
    }

    /**
     * Returns true if the specified offset exists in the items.
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Get the item at the specified offset.
     * @param mixed $offset
     * @return object
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }

    /**
     * Sets the item at the specified offset.
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->items[$offset] = $value;
    }

    /**
     * Removes the item at the specified offset.
     * @param mixed $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * Gets the number of items in the current page.
     * @return int
     */
    public function count(): int
    {
        return (int)count($this->items);
    }

    /**
     * Gets the items in the current page.
     * @return array<object>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Get the index of the first item in the page.
     * @return int|null
     */
    public function firstItem(): ?int
    {
        if (count($this->items) > 0) {
            return ($this->currentPage - 1) * $this->perPage + 1;
        }
        return null;
    }

    /**
     * Get the index of the last item in the page.
     * @return int|null
     */
    public function lastItem(): ?int
    {
        if (count($this->items) > 0) {
            return $this->firstItem() + $this->count() - 1;
        }
        return null;
    }

    /**
     * Get the current number of items per page in the pagination.
     * @return int
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get the total number of items we are paginating through.
     * @return int
     */
    public function total(): int
    {
        return $this->totalItems;
    }

    /**
     * Get the number of the last page.
     * @return int
     */
    public function lastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * Get the number of the current page.
     * @return int
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Return true if this is the first page.
     * @return bool
     */
    public function onFirstPage(): bool
    {
        return $this->currentPage() <= 1;
    }

    /**
     * Return true if there are more pages after this page.
     * @return bool
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage() < $this->lastpage();
    }

    /**
     * Returns the iterator for the paginator
     * @return ArrayIterator<int, mixed>
     */
    public function getIterator(): ArrayIterator
    {
        return new \ArrayIterator($this->items());
    }
}
