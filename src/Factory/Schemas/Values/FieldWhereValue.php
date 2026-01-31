<?php

namespace Lkt\Factory\Schemas\Values;

use Lkt\QueryBuilding\Where;

final class FieldWhereValue
{
    private array $value = [];

    public function __construct($value)
    {
        if (!$value) {
            $value = [];
        }

        if (!is_array($value)) {
            $value = [$value];
        }
        $this->value = $value;
    }

    public function getValue(): array
    {
        $r = [];
        foreach ($this->value as $v) {
            if ($v instanceof Where) {
                $r[] = $v->whereConstraintsToString();
            }
        }
        return $r;
    }
}