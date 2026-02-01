<?php

namespace Lkt\Instances;

use Lkt\Controllers\LktPermissionController;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Schemas\Schema;
use Lkt\Generated\GeneratedLktUserRole;
use Lkt\Users\Enums\RoleCapability;

class LktUserRole extends GeneratedLktUserRole
{
    const COMPONENT = 'lkt-user-role';

    public function getDefinedRoleCapability(string $component, string $permission, AbstractInstance|null $instance = null, bool $adminAccess = false): ?RoleCapability
    {
        // Firstly, check if there is a component without any kind of configuration
        // which attempts to always granted
        if (!LktPermissionController::hasComponentRegistered($component)) return null;

        // Secondly, check if that component has always granted/rejected that specific permission
        $capability = $adminAccess ? LktPermissionController::getEnsuredAdminPermission($component, $permission) : LktPermissionController::getEnsuredPermission($component, $permission);

        // Finally, check if there is a configured permission
        if (!$capability) {
            $haystack = $this->getPermissions();
            if (!isset($haystack[$component])) return null;
            if (!isset($haystack[$component][$permission])) return null;
            $capability = RoleCapability::tryFrom($haystack[$component][$permission]);
        }
        return $capability;
    }

    public function hasPermission(string $component, string $permission, AbstractInstance|null $instance = null, bool $adminAccess = false): bool
    {
        // Firstly, check if there is a component without any kind of configuration
        // which attempts to always granted
        if (!LktPermissionController::hasComponentRegistered($component)) return true;

        // Secondly, check if that component has always granted/rejected that specific permission
        $capability = $adminAccess ? LktPermissionController::getEnsuredAdminPermission($component, $permission) : LktPermissionController::getEnsuredPermission($component, $permission);

        // Finally, check if there is a configured permission
        if (!$capability) {
            $haystack = $this->getPermissions();
            if (!isset($haystack[$component])) return false;
            if (!isset($haystack[$component][$permission])) return false;
            $capability = RoleCapability::tryFrom($haystack[$component][$permission]);
        }

        // Check capability
        if ($capability) {
            switch ($capability) {
                case RoleCapability::Disabled:
                    return false;

                case RoleCapability::All:
                    return true;

                case RoleCapability::Owned:
                    if (is_object($instance)) {
                        $schema = Schema::get($component);
                        $ownershipField = $schema->getOwnershipField();
                        if (!$ownershipField) return false;

                        $ownerUserId = $instance->callOwnMethod($ownershipField->getGetterForPrimitiveValue(), []);
                        return $ownerUserId === LktUser::getSignedInUserId();
                    }
                    return true;
            }
        }

        return false;
    }

    public function postProcessRead(array $response): array
    {
        if ($this->accessPolicy) {
            if ($this->accessPolicy->name === 'lkt-related') return $response;
        }
        $response['permissions'] = $this->convertPermissionsValueToTableFormat();
        return $response;
    }

    public function preparePermissions(): array
    {
        $currentConfig = $this->getPermissions();
        if (!$currentConfig) $currentConfig = [];
        $permissions = LktPermissionController::getAllManageablePermission();
        $r = [];

        foreach ($permissions as $component => $perms) {
            $t = [];

            foreach ($perms as $perm) {
                $value = RoleCapability::Disabled->value;
                if (isset($currentConfig[$component])) {
                    $valid = RoleCapability::tryFrom($currentConfig[$component][$perm]);
                    if ($valid) $value = $valid->value;
                }
                $t[$perm] = $value;
            }

            $r[$component] = $t;
        }

        return $r;
    }

    public function convertPermissionsValueToTableFormat(): array
    {
        $haystack = $this->preparePermissions();
        $r = [];

        foreach ($haystack as $component => $perms) {
            $r[] = [
                'component' => $component,
                ...$perms,
            ];
        }

        return $r;
    }

    public function convertTablePermissionsToValueFormat(array $haystack): array
    {
        $r = [];
        foreach ($haystack as $item) {
            $t = $item;
            $component = $t['component'];
            unset($t['component']);
            $r[$component] = $t;
        }
        return $r;
    }

    public function prepareCrudData(array $data, CrudOperation|null $operation = null): array
    {
        $data['permissions'] = is_array($data['permissions']) ? $data['permissions'] : [];
        $data['permissions'] = $this->convertTablePermissionsToValueFormat($data['permissions']);
        return $data;
    }
}