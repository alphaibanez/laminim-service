<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Values\ComponentValue;

trait FieldWithRelatedAccessPolicyOptionTrait
{
    protected array $relatedAccessPolicies = [];

    public function setRelatedAccessPolicies(array $relatedAccessPolicies): static
    {
        $this->relatedAccessPolicies = $relatedAccessPolicies;
        return $this;
    }

    public function getRelatedAccessPolicies(): array
    {
        return $this->relatedAccessPolicies;
    }

    public function getAssociatedAccessPolicy(string $accessPolicy): ?string
    {
        return $this->relatedAccessPolicies[$accessPolicy];
    }
}