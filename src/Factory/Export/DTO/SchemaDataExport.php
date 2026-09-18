<?php

namespace Lkt\Factory\Export\DTO;

use Lkt\Factory\Instance\Interfaces\Item;

class SchemaDataExport extends AbstractDataExport
{
    readonly public array $header;
    readonly public array $rows;

    /**
     * @param Item[] $items
     * @param string $accessPolicy
     */
    protected function __construct(array $items, string $accessPolicy = '')
    {
        $rows = [];
        foreach ($items as $item) {
            if ($accessPolicy) $item->setAccessPolicy($accessPolicy);
            $rows[] = $item->autoRead();
        }

        $this->rows = $rows;
        $this->header = count($rows) === 0 ? [] : array_keys($rows[0]);
    }

    /**
     * @param Item[] $items
     * @param string $accessPolicy
     * @return static
     */
    public static function fromItems(array $items, string $accessPolicy = ''): static
    {
        return new static($items, $accessPolicy);
    }
}