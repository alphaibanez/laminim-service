<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\RelatedItemDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithRelatedItemDataTrait
{
    protected RelatedItemDataController $relatedItemData;

    protected function initRelatedItemData(Schema $schema, Item $item, array $rawData): static
    {
        $this->relatedItemData = new RelatedItemDataController($schema, $item, $rawData);
        return $this;
    }
}