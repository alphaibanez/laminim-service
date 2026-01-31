<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Values\FieldWhereValue;

trait FieldWithWhereOptionTrait
{
    protected ?FieldWhereValue $where = null;

    /**
     * @param $where
     * @return $this
     */
    final public function setWhere($where): static
    {
        $this->where = new FieldWhereValue($where);
        return $this;
    }

    final public function getWhere(): array
    {
        if ($this->where instanceof FieldWhereValue) {
            return $this->where->getValue();
        }
        return [];
    }
}