<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\Locale\Locale;

class I18nStringNotInConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        if (count($this->value) === 0) {
            return '';
        }
        $values = array_map(function($v){ return addslashes(stripslashes($v));}, $this->value);
        $value = "('".implode("','", $values)."')";

        $prepend = $this->getTablePrepend();

        $lang = Locale::getLangCode();
        if (!$lang) $lang = 'en';

        return "JSON_UNQUOTE(JSON_EXTRACT({$prepend}{$this->column}, \"$.{$lang}\")) NOT IN {$value}";
    }
}