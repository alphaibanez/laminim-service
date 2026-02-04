<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Traits\FieldWithChoiceOptionTrait;

class IntegerChoiceField extends IntegerField
{
    const TYPE = 'integer-choice';

    protected string $prefabRole = '';

    use FieldWithChoiceOptionTrait;

    public function setPrefabRole(string $role): static
    {
        $this->prefabRole = $role;
        return $this;
    }

    public function getPrefabRole(): string
    {
        return $this->prefabRole;
    }
}