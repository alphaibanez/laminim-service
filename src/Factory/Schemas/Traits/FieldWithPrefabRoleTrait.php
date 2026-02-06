<?php

namespace Lkt\Factory\Schemas\Traits;

use Lkt\Factory\Schemas\Enums\PrefabRole;

trait FieldWithPrefabRoleTrait
{
    protected PrefabRole $prefabRole = PrefabRole::None;

    public function setPrefabRole(PrefabRole $role): static
    {
        $this->prefabRole = $role;
        return $this;
    }

    public function getPrefabRole(): PrefabRole
    {
        return $this->prefabRole;
    }
}