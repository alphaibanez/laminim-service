<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\PivotDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithPivotDataTrait
{
    protected PivotDataController $pivotData;

    protected function initPivotData(Schema $schema, Item $item, bool $refreshing = false): static
    {
        if ($refreshing && isset($this->pivotData)) return $this;

        $this->pivotData = new PivotDataController($schema, $item);
        return $this;
    }

    public function linkPivot(string $key, $id): static
    {
        $this->pivotData->link($key, $id);
        return $this;
    }

    public function unlinkPivot(string $key, $id): static
    {
        $this->pivotData->unlink($key, $id);
        return $this;
    }
}