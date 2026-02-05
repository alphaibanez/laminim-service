<?php

namespace Lkt\Http\DTO;

class GrantedPermsAttempt
{
    private function __construct(
        readonly public string $type = 'simple',
        readonly public array  $publicPerms = [],
        readonly public array  $loggedPerms = [],
        readonly public array  $adminPerms = [],
    )
    {
    }

    public static function simple(array $perms): static
    {
        return new static('simple', $perms);
    }

    public static function perAccessLevel(array $publicPerms, array $loggedPerms, array $adminPerms): static
    {
        return new static('per-access-level', $publicPerms, $loggedPerms, $adminPerms);
    }
}