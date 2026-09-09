<?php

namespace Lkt\Factory\Fields\Traits;

use Lkt\Factory\Schemas\Enums\RelatedFieldClonePolicy;

trait FieldWithRelatedClonePolicyOptionTrait
{
    protected RelatedFieldClonePolicy $relatedFieldClonePolicy = RelatedFieldClonePolicy::Ignore;

    final public function setRelatedFieldClonePolicy(RelatedFieldClonePolicy $relatedFieldClonePolicy): static
    {
        $this->relatedFieldClonePolicy = $relatedFieldClonePolicy;
        return $this;
    }

    final public function getRelatedFieldClonePolicy(): RelatedFieldClonePolicy
    {
        return $this->relatedFieldClonePolicy;
    }
}