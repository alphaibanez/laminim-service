<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\ForeignKeyDataController;
use Lkt\Factory\Instance\DataControllers\RelatedItemDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithRelatedItemDataTrait
{
    private RelatedItemDataController $relatedItemData;

    private function initRelatedItemData(Schema $schema, Item $item, array $rawData): static
    {
        $this->relatedItemData = new RelatedItemDataController($schema, $item, $rawData);
        return $this;
    }
}