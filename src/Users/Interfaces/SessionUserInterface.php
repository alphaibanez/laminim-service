<?php

namespace Lkt\Users\Interfaces;

use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Http\DTO\GrantedPermsAttempt;
use Lkt\Http\Enums\AccessLevel;
use Lkt\Users\Enums\RoleCapability;

interface SessionUserInterface
{
    public function getId(): int|null;

    public function signIn(): static;
    public function signOut(): static;

    public static function getSignedInUserId(): int;
    public static function getSignedInUser(): ?static;
    public static function signedIn(): bool;
    public function hasAdminAccess(): bool;
    public function hasAppPermission(string $component, string $permission, AbstractInstance|Item|null $instance = null): bool;
    public function hasAdminPermission(string $component, string $permission, AbstractInstance|Item|null $instance = null): bool;
    public function getAppCapability(string $component, string $permission, AbstractInstance|Item|null $instance = null):? RoleCapability;
    public function getAdminCapability(string $component, string $permission, AbstractInstance|Item|null $instance = null):? RoleCapability;
    public function attemptToGrantPermissions(AccessLevel $accessLevel, string $component, GrantedPermsAttempt $grantedPermsAttempt, AbstractInstance|Item|null $instance = null): array;
}