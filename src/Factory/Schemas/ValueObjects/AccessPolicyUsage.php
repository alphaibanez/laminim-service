<?php

namespace Lkt\Factory\Schemas\ValueObjects;

use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;

class AccessPolicyUsage
{
    public string $component;
    public string $name;
    public AccessPolicyEndOfLife $endOfLife;

    public function __construct(string $component, string $name, AccessPolicyEndOfLife $endOfLife)
    {
        $this->name = $name;
        $this->component = $component;
        $this->endOfLife = $endOfLife;
    }

    public function matchedEndOfLife(AccessPolicyEndOfLife $endOfLife): bool
    {
        return $endOfLife->value === $this->endOfLife->value;
    }
}