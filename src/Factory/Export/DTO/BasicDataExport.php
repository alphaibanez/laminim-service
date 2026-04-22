<?php

namespace Lkt\Factory\Export\DTO;

class BasicDataExport extends AbstractDataExport
{
    readonly public array $header;
    readonly public array $rows;

    /**
     * @param array[] $items
     */
    protected function __construct(array $items, array $header = [])
    {
        $this->rows = $items;
        $this->header = $header;
    }

    public static function fromData(array $items, array $header = []): static
    {
        return new static($items, $header);
    }
}