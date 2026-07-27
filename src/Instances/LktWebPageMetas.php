<?php

namespace Lkt\Instances;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Schemas\Schema;
use Lkt\Generated\GeneratedLktWebPageMetas;

class LktWebPageMetas extends GeneratedLktWebPageMetas
{
    const COMPONENT = 'lkt-web-page-metas';

    public static function fromSlug(string $slug): static|null
    {
        $slug = explode('/', $slug);
        $slug = $slug[count($slug) - 1];

        $schema = Schema::get(LaminimComponent::WebPageMetas->value);
        return static::getOne($schema->getQueryBuilder()->andSlugEqual($slug));
    }
}