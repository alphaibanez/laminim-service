<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Instance\Enums\EmptyDataMode;

trait FieldWithEmptyDataModeTrait
{
    protected EmptyDataMode $emptyDataMode = EmptyDataMode::NullAndEmpty;

    public function getEmptyDataMode(): EmptyDataMode
    {
        return $this->emptyDataMode;
    }

    public function setEmptyDataMode(EmptyDataMode $mode): static
    {
        $this->emptyDataMode = $mode;
        return $this;
    }
}