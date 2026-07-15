<?php

namespace Lkt\Factory\Schemas\Fields;

class SingleRelatedField extends RelatedField
{
    public static function defineRelation(string $component, string $name, string $column = ''): static
    {
        return (new static($name, $column))->setComponent($component)->setSingleMode();
    }
}