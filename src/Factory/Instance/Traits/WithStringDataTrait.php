<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\DataControllers\StringDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait WithStringDataTrait
{
    private StringDataController $stringData;

    private function initStringData(Schema $schema, Item $item, array $rawData): static
    {
        $this->stringData = new StringDataController($schema, $item, $rawData);
        return $this;
    }
}