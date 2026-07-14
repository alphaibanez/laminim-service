<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\Schema;
use Lkt\Factory\Schemas\ValueObjects\AccessPolicy;
use Lkt\Factory\Schemas\ValueObjects\AccessPolicyUsage;

trait ItemWithAccessPolicyTrait
{
    protected AccessPolicyUsage|null $accessPolicy = null;

    public function setAccessPolicy(string|AccessPolicy $accessPolicy, AccessPolicyEndOfLife $accessPolicyEndOfLife = AccessPolicyEndOfLife::UntilUpdated): static
    {
        if ($accessPolicy instanceof AccessPolicy) {
            $this->accessPolicy = new AccessPolicyUsage(static::COMPONENT, $accessPolicy->name, $accessPolicyEndOfLife);
        } else {
            $schema = Schema::get(static::COMPONENT);
            $isAnonymous = $this->isAnonymous();
            if ($accessPolicyEndOfLife === AccessPolicyEndOfLife::UntilNextWrite) {
                $modifier = $isAnonymous ? 'mk' : 'up';
                if ($schema->hasAccessPolicy("{$modifier}:$accessPolicy")) {
                    $accessPolicy = "{$modifier}:$accessPolicy";

                } elseif ($schema->hasAccessPolicy("w:$accessPolicy")) {
                    $accessPolicy = "w:$accessPolicy";
                }
            } elseif ($accessPolicyEndOfLife === AccessPolicyEndOfLife::UntilNextRead) {
                if ($schema->hasAccessPolicy("r:$accessPolicy")) {
                    $accessPolicy = "r:$accessPolicy";
                }
            }

            $this->accessPolicy = new AccessPolicyUsage(static::COMPONENT, $accessPolicy, $accessPolicyEndOfLife);
        }
        return $this;
    }

    public function getAccessPolicyUsage(): ?AccessPolicyUsage
    {
        return $this->accessPolicy;
    }
}