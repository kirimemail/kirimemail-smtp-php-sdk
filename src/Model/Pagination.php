<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Model;

use JsonSerializable;

class Pagination implements JsonSerializable
{
    private ?int $total;
    private ?int $page;
    private ?int $limit;
    private ?int $offset;

    public function __construct(array $data = [])
    {
        $this->total = $data['total'] ?? null;
        $this->page = $data['page'] ?? null;
        $this->limit = $data['limit'] ?? null;
        $this->offset = $data['offset'] ?? null;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): Pagination
    {
        $this->total = $total;
        return $this;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): Pagination
    {
        $this->page = $page;
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): Pagination
    {
        $this->limit = $limit;
        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(?int $offset): Pagination
    {
        $this->offset = $offset;
        return $this;
    }

    public function getTotalPages(): ?int
    {
        if ($this->total === null || $this->limit === null || $this->limit === 0) {
            return null;
        }
        return (int) ceil($this->total / $this->limit);
    }

    public function hasNextPage(): bool
    {
        $totalPages = $this->getTotalPages();
        return $totalPages !== null && $this->page !== null && $this->page < $totalPages;
    }

    public function hasPreviousPage(): bool
    {
        return $this->page !== null && $this->page > 1;
    }

    public function getNextPage(): ?int
    {
        return $this->hasNextPage() ? ($this->page ?? 0) + 1 : null;
    }

    public function getPreviousPage(): ?int
    {
        return $this->hasPreviousPage() ? ($this->page ?? 0) - 1 : null;
    }

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'page' => $this->page,
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}