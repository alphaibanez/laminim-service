<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\DateDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithDateDataTrait
{
    private DateDataController $dateData;

    private function initDateData(Schema $schema, Item $item, array $rawData): static
    {
        $this->dateData = new DateDataController($schema, $item, $rawData);
        return $this;
    }
}