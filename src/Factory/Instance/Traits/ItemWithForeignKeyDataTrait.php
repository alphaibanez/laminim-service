<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\ForeignKeyDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithForeignKeyDataTrait
{
    protected ForeignKeyDataController $foreignKeyD;

    protected function initForeignKeyData(Schema $schema, Item $item, array $rawData): static
    {
        $this->foreignKeyD = new ForeignKeyDataController($schema, $item, $rawData);
        return $this;
    }
}