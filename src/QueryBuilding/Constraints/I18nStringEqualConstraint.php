<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\Locale\Locale;
use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class I18nStringEqualConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $column = $this->column;
        $value = $this->value;
        if (str_starts_with($value, 'COMPRESS(')) {
            return "{$column}={$value}";
        }

        $v = addslashes(stripslashes($value));
        $prepend = $this->getTablePrepend();

        $lang = Locale::getLangCode();
        if (!$lang) $lang = 'en';

        return "JSON_UNQUOTE(JSON_EXTRACT({$prepend}{$column}, \"$.{$lang}\")) = '{$v}'";
    }
}