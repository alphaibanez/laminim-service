<?php

namespace Lkt\QueryBuilding\Constraints;

use Lkt\Locale\Locale;

class I18nStringLikeConstraint extends AbstractConstraint
{
    public function __toString(): string
    {
        $v = addslashes(stripslashes($this->value));
        $prepend = $this->getTablePrepend();

        $lang = Locale::getLangCode();
        if (!$lang) $lang = 'en';

        return "JSON_CONTAINS({$prepend}{$this->column}, '{$v}', \"$.{$lang}\")";

//        $lang = Locale::getLangCode();
//        if (!$lang) $lang = 'en';
//
//        return "JSON_UNQUOTE(JSON_EXTRACT({$prepend}{$this->column}, \"$.{$lang}\")) LIKE '%{$v}%'";
    }
}