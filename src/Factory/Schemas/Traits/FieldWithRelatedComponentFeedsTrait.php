<?php

namespace Lkt\Factory\Schemas\Traits;

trait FieldWithRelatedComponentFeedsTrait
{

    protected $relatedComponentFeeds = [];


    public function addRelatedComponentFeed(string $column, $value): static
    {
        $this->relatedComponentFeeds[$column] = $value;
        return $this;
    }

    public function getRelatedComponentFeeds(): array
    {
        return $this->relatedComponentFeeds;
    }
}