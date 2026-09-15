<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Attributes\Deprecated;

/**
 * @deprecated use RelatedField::single instead
 */
#[Deprecated]
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