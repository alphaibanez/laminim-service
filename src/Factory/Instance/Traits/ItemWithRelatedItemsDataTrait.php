<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\RelatedItemsDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithRelatedItemsDataTrait
{
    protected RelatedItemsDataController $relatedItemsD;

    protected function initRelatedItemsData(Schema $schema, Item $item, array $rawData, bool $refreshing = false): static
    {
        if ($refreshing && isset($this->relatedItemsD)) return $this;

        $this->relatedItemsD = new RelatedItemsDataController($schema, $item, $rawData);
        return $this;
    }
}