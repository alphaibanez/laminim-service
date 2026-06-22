<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\UnixTimeStampDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithUnixTimestampDataTrait
{
    private UnixTimeStampDataController $unixTimeStampData;

    private function initUnixTimeStampData(Schema $schema, Item $item, array $rawData): static
    {
        $this->unixTimeStampData = new UnixTimeStampDataController($schema, $item, $rawData);
        return $this;
    }
}