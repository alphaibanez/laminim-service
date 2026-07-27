<?php

namespace Lkt\Factory\Instance\Traits;

use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\ValueObjects\AccessPolicy;
use Lkt\Factory\Schemas\ValueObjects\AccessPolicyUsage;

trait ItemWithAccessPolicyTrait
{
    protected AccessPolicyUsage|null $accessPolicy = null;

    public function setAccessPolicy(string|AccessPolicy $accessPolicy, AccessPolicyEndOfLife $accessPolicyEndOfLife = AccessPolicyEndOfLife::UntilUpdated): static
    {
        $schema = $this->getSchema();
        if ($accessPolicy instanceof AccessPolicy) {
            $this->accessPolicy = new AccessPolicyUsage($schema->getComponent(), $accessPolicy->name, $accessPolicyEndOfLife);
        } else {
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

            $this->accessPolicy = new AccessPolicyUsage($schema->getComponent(), $accessPolicy, $accessPolicyEndOfLife);
        }
        return $this;
    }

    public function getAccessPolicyUsage(): ?AccessPolicyUsage
    {
        if (!isset($this->accessPolicy)) return null;
        return $this->accessPolicy;
    }
}