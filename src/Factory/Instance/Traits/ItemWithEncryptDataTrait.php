<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\EncryptDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithEncryptDataTrait
{
    protected EncryptDataController $encryptD;

    protected function initEncryptData(Schema $schema, Item $item, array $rawData): static
    {
        $this->encryptD = new EncryptDataController($schema, $item, $rawData);
        return $this;
    }
}