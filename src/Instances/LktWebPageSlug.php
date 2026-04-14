<?php

namespace Lkt\Instances;

use Lkt\Debug\VarDumper;
use Lkt\Generated\GeneratedLktWebPageSlug;

class LktWebPageSlug extends GeneratedLktWebPageSlug
{
    const COMPONENT = 'lkt-web-page-slug';

    public static function fromSlug(string $slug): static|null
    {
        $slug = explode('/', $slug);
        $slug = $slug[count($slug) - 1];

        return static::getOne(static::getQueryCaller()->andSlugEqual($slug));
    }
}