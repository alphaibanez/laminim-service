<?php

namespace Lkt\Http\DTO;

class TargetAccessPolicy
{
    private function __construct(
        readonly public string $type = 'simple',
        readonly public string  $public = '',
        readonly public string  $logged = '',
        readonly public string  $admin = '',
    )
    {
    }

    public static function simple(string $public): static
    {
        return new static('simple', $public);
    }

    public static function perAccessLevel(string $public, string $logged, string $admin): static
    {
        return new static('per-access-level', $public, $logged, $admin);
    }
}