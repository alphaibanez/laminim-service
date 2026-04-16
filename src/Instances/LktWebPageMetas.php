<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktWebPageMetas;

class LktWebPageMetas extends GeneratedLktWebPageMetas
{
    const COMPONENT = 'lkt-web-page-metas';

    public static function fromSlug(string $slug): static|null
    {
        $slug = explode('/', $slug);
        $slug = $slug[count($slug) - 1];

        return static::getOne(static::getQueryCaller()->andSlugEqual($slug));
    }
}