<?php

namespace Lkt\QueryBuilding\Helpers;

class ColumnHelper
{
    public static function buildColumnString(string $column, string $table): string
    {
        $prependTable = "{$table}.";
        $tempColumn = str_replace([' as ', ' AS ', ' aS ', ' As '], '{{---LKT_SEPARATOR---}}', $column);
        $exploded = explode('{{---LKT_SEPARATOR---}}', $tempColumn);

        $key = trim($exploded[0]);
        $alias = isset($exploded[1]) ? trim($exploded[1]) : '';

        if (str_starts_with($column, 'UNCOMPRESS')) {
            if ($alias !== '') {
                $r = "{$key} AS {$alias}";
            } else {
                $r = $key;
            }
        }

        elseif (str_starts_with($key, $prependTable)) {
            if ($alias !== '') {
                $r = "{$key} AS {$alias}";
            } else {
                $r = $key;
            }
        } else {
            if ($alias !== '') {
                $r = "{$prependTable}{$key} AS {$alias}";
            } else {
                $r = "{$prependTable}{$key}";
            }
        }

        return $r;
    }

    public static function makeUpdateParams(array $params = []) :string
    {
        $r = [];
        foreach ($params as $field => $value) {
            $v = addslashes(stripslashes($value));
            if (str_starts_with($value, 'COMPRESS(')){
                $r[] = "`{$field}`={$value}";
            } else {
                $r[] = "`{$field}`='{$v}'";
            }
        }
        return trim(implode(',', $r));
    }
}