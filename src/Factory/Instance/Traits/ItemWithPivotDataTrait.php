<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Instance\DataControllers\PivotDataController;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Schema;

trait ItemWithPivotDataTrait
{
    protected PivotDataController $pivotD;

    protected function initPivotData(Schema $schema, Item $item, bool $refreshing = false): static
    {
        if ($refreshing && isset($this->pivotD)) return $this;

        $this->pivotD = new PivotDataController($schema, $item);
        return $this;
    }

    public function linkPivot(string $key, $id): static
    {
        $this->pivotD->link($key, $id);
        return $this;
    }

    public function unlinkPivot(string $key, $id): static
    {
        $this->pivotD->unlink($key, $id);
        return $this;
    }
}