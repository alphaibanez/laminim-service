<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\MultipleStringDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithMultipleStringDataTrait
{
    protected MultipleStringDataController $stringMD;

    protected function initMultipleStringData(Schema $schema, Item $item, array $rawData): static
    {
        $this->stringMD = new MultipleStringDataController($schema, $item, $rawData);
        return $this;
    }
}