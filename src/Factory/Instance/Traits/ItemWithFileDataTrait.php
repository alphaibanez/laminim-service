<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\FileDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithFileDataTrait
{
    protected FileDataController $fileD;

    protected function initFileData(Schema $schema, Item $item, array $rawData): static
    {
        $this->fileD = new FileDataController($schema, $item, $rawData);
        return $this;
    }
}