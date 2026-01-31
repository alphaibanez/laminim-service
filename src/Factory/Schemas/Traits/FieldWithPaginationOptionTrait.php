<?php

namespace Lkt\Factory\Schemas\Traits;

trait FieldWithPaginationOptionTrait
{
    protected int $itemsPerPage = -1;
    protected string $countableField = '';

    final public function setPagination(int $itemsPerPage = 20, string $countableField = ''): self
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->countableField = $countableField;
        return $this;
    }

    final public function hasPagination(): bool
    {
        return $this->itemsPerPage > 0;
    }

    final public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    final public function getCountableField(): string
    {
        return $this->countableField;
    }
}