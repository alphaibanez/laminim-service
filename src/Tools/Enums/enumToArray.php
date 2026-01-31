<?php

namespace Lkt\Tools\Enums;

function enumToArray(string $enumClass): array
{
    if (enum_exists($enumClass)) {
        $enum = new \ReflectionEnum($enumClass);
        $cases = $enum->getCases();

        $r = [];
        foreach ($cases as $case) {
            $r[$case->getName()] = $case->getValue()->value;
        }

        return $r;
    }
    return [];
}