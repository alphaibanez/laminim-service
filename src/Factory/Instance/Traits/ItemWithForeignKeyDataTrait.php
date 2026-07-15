<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\BooleanDataController;
use Lkt\Factory\Instance\DataControllers\ForeignKeyDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithForeignKeyDataTrait
{
    protected ForeignKeyDataController $foreignKeyData;

    private function initForeignKeyData(Schema $schema, Item $item, array $rawData): static
    {
        $this->foreignKeyData = new ForeignKeyDataController($schema, $item, $rawData);
        return $this;
    }
}