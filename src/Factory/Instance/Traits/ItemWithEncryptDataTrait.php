<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\EncryptDataController;
use Lkt\Factory\Instance\DataControllers\StringDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithEncryptDataTrait
{
    protected EncryptDataController $encryptData;

    private function initEncryptData(Schema $schema, Item $item, array $rawData): static
    {
        $this->encryptData = new EncryptDataController($schema, $item, $rawData);
        return $this;
    }
}