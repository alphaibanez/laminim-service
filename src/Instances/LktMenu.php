<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktMenu;
use Lkt\Menus\Enums\MenuEntryType;
use Lkt\Users\Instances\LktUser;
use Lkt\WebItems\WebItem;

class LktMenu extends GeneratedLktMenu
{
    const COMPONENT = 'lkt-menu';

    public function getNavigableEntries(): array
    {
        $haystack = $this->getEntries();
        $filtered = [];
        $user = LktUser::getSignedInUser();

        $nativeIncludedAdminWebItems = [];

        foreach ($haystack as $entry){
            switch ($entry->getType()) {
                case MenuEntryType::RelativeUrl->value:
                case MenuEntryType::FullUrl->value:

                    if ($entry->accessLevelIsOnlyAdminUsers()) {
                        if ($user->hasAdminAccess()) $filtered[] = $entry;
                    }
                    elseif ($entry->accessLevelIsOnlyLoggedUsers()) {
                        if ($user) $filtered[] = $entry;
                    } else {
                        $filtered[] = $entry;
                    }
                    break;

                case MenuEntryType::WebItems->value:
                    if (!$user) break;
                    if ($entry->accessLevelIsOnlyAdminUsers()) {
                        if ($user->hasAdminPermission($entry->getComponent(), 'ls')) {
                            $filtered[] = $entry;
                            $nativeIncludedAdminWebItems[] = $entry->getComponent();
                        }
                    }
                    elseif ($entry->accessLevelIsOnlyLoggedUsers()) {
                        if ($user->hasAdminPermission($entry->getComponent(), 'ls')) $filtered[] = $entry;
                    }
                    break;
            }
        }

        $r = [];
        foreach ($filtered as $entry) {
            $r[] = $entry->setAccessPolicy('r-app-menu')->autoRead();
        }

        if ($this->includeAvailableAdminRoutes() && ($user->isAdministrator() || $user->hasAdminAccess())) {
            foreach (WebItem::getAll() as $webItem) {
                if (in_array($webItem->publicComponentName, $nativeIncludedAdminWebItems)) continue;
                if (!$user->hasAdminPermission($webItem->component, 'ls')) continue;

                $anonymousEntry = LktMenuEntry::getInstance()
                    ->setType(MenuEntryType::WebItems->value)
                    ->setComponent($webItem->publicComponentName ?? $webItem->component)
                ;
                $r[] = $anonymousEntry->setAccessPolicy('r-app-menu')->autoRead();
            }
        }
        return $r;
    }
}