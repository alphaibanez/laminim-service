<?php

namespace Lkt\Tools\Url;

function cleanUrl(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }
    if (preg_match("/^(http[s]?\:\/\/|ftp[s]?\:\/\/?|\/)/i", $url) === 0) {
        return "//$url";
    }

    return trim(str_replace([
        'http://',
        'https://',
        'ftp://',
        'ftps://',
    ], '//', $url));
}