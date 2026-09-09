<?php

namespace Lkt\Factory\Fields\Traits;

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