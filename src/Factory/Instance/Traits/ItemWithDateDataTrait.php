<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\DateDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithDateDataTrait
{
    protected DateDataController $dateD;

    protected function initDateData(Schema $schema, Item $item, array $rawData): static
    {
        $this->dateD = new DateDataController($schema, $item, $rawData);
        return $this;
    }
}