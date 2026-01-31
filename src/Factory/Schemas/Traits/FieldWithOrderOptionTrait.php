<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Values\FieldOrderValue;

trait FieldWithOrderOptionTrait
{
    protected ?FieldOrderValue $order = null;

    /**
     * @return FieldWithOrderOptionTrait
     */
    final public function setOrder($order): static
    {
        $this->order = new FieldOrderValue($order);
        return $this;
    }

    final public function getOrder(): array
    {
        if ($this->order instanceof FieldOrderValue) {
            return $this->order->getValue();
        }
        return [];
    }
}