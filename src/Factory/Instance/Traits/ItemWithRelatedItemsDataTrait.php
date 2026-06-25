<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\RelatedItemsDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithRelatedItemsDataTrait
{
    private RelatedItemsDataController $relatedItemsData;

    private function initRelatedItemsData(Schema $schema, Item $item, array $rawData): static
    {
        $this->relatedItemsData = new RelatedItemsDataController($schema, $item, $rawData);
        return $this;
    }
}