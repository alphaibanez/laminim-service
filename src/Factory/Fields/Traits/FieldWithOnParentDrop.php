<?php

namespace Lkt\Factory\Fields\Traits;

use Lkt\Factory\Fields\Enums\OnParentDrop;

trait FieldWithOnParentDrop
{
    protected OnParentDrop $onParentDrop = OnParentDrop::SetNull;
    protected mixed $customValue = null;

    public function getOnParentDrop(): OnParentDrop
    {
        return $this->onParentDrop;
    }

    public function setOnParentDrop(OnParentDrop $mode, $customValue = null): static
    {
        $this->onParentDrop = $mode;
        $this->customValue = $customValue;
        return $this;
    }
}