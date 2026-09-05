<?php

namespace Lkt\Factory\Schemas\Fields;

/**
 * @deprecated use RelatedField::single instead
 */
class SingleRelatedField extends RelatedField
{
    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->component = $component;
        $ins->singleMode = true;
        return $ins;
    }
}