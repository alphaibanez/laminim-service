<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\MultipleIntegerDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithMultipleIntegerDataTrait
{
    protected MultipleIntegerDataController $integerMD;

    protected function initMultipleIntegerData(Schema $schema, Item $item, array $rawData): static
    {
        $this->integerMD = new MultipleIntegerDataController($schema, $item, $rawData);
        return $this;
    }
}