<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\RelatedItemsDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithRelatedItemsDataTrait
{
    protected RelatedItemsDataController $relatedItemsData;

    private function initRelatedItemsData(Schema $schema, Item $item, array $rawData, bool $refreshing = false): static
    {
        if ($refreshing && isset($this->relatedItemsData)) return $this;

        $this->relatedItemsData = new RelatedItemsDataController($schema, $item, $rawData);
        return $this;
    }
}