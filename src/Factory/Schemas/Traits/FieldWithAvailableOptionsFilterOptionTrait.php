<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Values\FieldWhereValue;

trait FieldWithAvailableOptionsFilterOptionTrait
{
    protected ?FieldWhereValue $availableOptionsFilter = null;

    /**
     * @param $where
     * @return $this
     */
    final public function setAvailableOptionsFilter($where): static
    {
        $this->availableOptionsFilter = new FieldWhereValue($where);
        return $this;
    }

    final public function getAvailableOptionsFilter(): array
    {
        if ($this->availableOptionsFilter instanceof FieldWhereValue) {
            return $this->availableOptionsFilter->getValue();
        }
        return [];
    }
}