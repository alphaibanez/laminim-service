<?php

namespace Lkt\Controllers;

use Lkt\Users\Enums\RoleCapability;

class LktPermissionController
{
    protected static $permissionsManagement = [];
    protected static $ensuredAppPermissions = [];
    protected static $ensuredAdminPermissions = [];

    public static function hasComponentRegistered(string $component): bool
    {
        return isset(static::$permissionsManagement[$component]);
    }

    public static function enablePermissionManagement(string $component, string|array $permission): void
    {
        if (!is_array(static::$permissionsManagement[$component])) static::$permissionsManagement[$component] = [];

        if (is_array($permission)) {
            foreach ($permission as $perm) {
                if (!in_array($perm, static::$permissionsManagement[$component])) static::$permissionsManagement[$component][] = $perm;
            }
            return;
        }
        if (!in_array($permission, static::$permissionsManagement[$component])) static::$permissionsManagement[$component][] = $permission;
    }

    public static function ensurePermission(string $component, string|array $permission, RoleCapability $capability = RoleCapability::Owned): void
    {
        if (!is_array($permission)) $permission = [$permission];
        if (!is_array(static::$ensuredAppPermissions[$component])) static::$ensuredAppPermissions[$component] = [];
        foreach ($permission as $perm) {
            if (!array_key_exists($perm, static::$ensuredAppPermissions[$component])) {
                static::$ensuredAppPermissions[$component][$perm] = $capability;
            }
        }
    }

    public static function ensureAdminPermission(string $component, string $permission, RoleCapability $capability = RoleCapability::Owned): void
    {
        if (!is_array(static::$ensuredAdminPermissions[$component])) static::$ensuredAdminPermissions[$component] = [];
        if (!array_key_exists($permission, static::$ensuredAdminPermissions[$component])) {
            static::$ensuredAdminPermissions[$component][$permission] = $capability;
        }
    }

    public static function getAllManageablePermission(): array
    {
        return static::$permissionsManagement;
    }

    public static function getEnsuredPermission(string $component, string $permission): null|RoleCapability
    {
        if (!isset(static::$ensuredAppPermissions[$component])) return null;
        if (!isset(static::$ensuredAppPermissions[$component][$permission])) return null;
        return static::$ensuredAppPermissions[$component][$permission];
    }

    public static function getEnsuredAdminPermission(string $component, string $permission): null|RoleCapability
    {
        if (!isset(static::$ensuredAdminPermissions[$component])) return null;
        if (!isset(static::$ensuredAdminPermissions[$component][$permission])) return null;
        return static::$ensuredAdminPermissions[$component][$permission];
    }
}