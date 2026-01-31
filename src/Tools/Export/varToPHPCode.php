<?php

namespace Lkt\Tools\Export;


// @see https://stackoverflow.com/questions/24316347/how-to-format-var-export-to-php5-4-array-syntax
function varToPHPCode($var, $indent = ''): string
{
    switch (gettype($var)) {
        case 'integer':
        case 'double':
            return $var;

        case 'string':
            return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';

        case 'array':
            $indexed = array_keys($var) === range(0, count($var) - 1);
            $r = [];
            foreach ($var as $key => $value) {
                $r[] = "$indent    "
                    . ($indexed ? "" : varToPHPCode($key) . " => ")
                    . varToPHPCode($value, "$indent    ");
            }
            return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";

        case 'boolean':
            return $var ? 'true' : 'false';

        default:
            return var_export($var, true);
    }
}