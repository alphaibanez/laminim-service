<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\ForeignKeyDataController;
use Lkt\Factory\Instance\DataControllers\ForeignKeysDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithForeignKeysDataTrait
{
    protected ForeignKeysDataController $foreignKeysData;

    private function initForeignKeysData(Schema $schema, Item $item, array $rawData): static
    {
        $this->foreignKeysData = new ForeignKeysDataController($schema, $item, $rawData);
        return $this;
    }
}