<?php

namespace Lkt\Tools\Strings;

function toAscii(string $str = ''): string
{
    $str = \str_replace(array('º', 'ª'), '', $str);
    return \utf8_encode(\strtr(\utf8_decode($str),
        \utf8_decode(
            'ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
        'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy'));
}