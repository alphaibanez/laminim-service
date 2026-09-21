<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\Locale\Locale;
use Lkt\QueryBuilding\Interfaces\QueryConstraint;

class I18nStringNotEndsLikeConstraint extends AbstractConstraint implements QueryConstraint
{
    public function __toString(): string
    {
        $v = addslashes(stripslashes($this->value));
        $prepend = $this->getTablePrepend();

        $lang = Locale::getLangCode();
        if (!$lang) $lang = 'en';

        return "JSON_UNQUOTE(JSON_EXTRACT({$prepend}{$this->column}, \"$.{$lang}\")) NOT LIKE '%{$v}'";
    }
}