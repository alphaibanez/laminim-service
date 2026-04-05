<?php

namespace Lkt\Instances;

use Lkt\Generated\GeneratedLktMenu;
use Lkt\Menus\Enums\MenuEntryType;
use Lkt\Translations\Translations;
use Lkt\WebItems\Enums\WebItemAdminMenuRegister;
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
                case MenuEntryType::AppRoute->value:
                case MenuEntryType::Header->value:
                case MenuEntryType::Parent->value:
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

        $groups = [];
        if ($this->includeAvailableAdminRoutes() && is_object($user) && ($user->isAdministrator() || $user->hasAdminAccess())) {
            $i = count($r);
            foreach (WebItem::getAll() as $webItem) {
                if ($webItem->includeInAdminMenu === WebItemAdminMenuRegister::Never) continue;
                if (in_array($webItem->publicComponentName, $nativeIncludedAdminWebItems)) continue;

                if ($webItem->includeInAdminMenu === WebItemAdminMenuRegister::OnlyAdministrator && !$user->isAdministrator()) continue;

                $hasPerm = $user->hasAdminPermission($webItem->component, 'ls');
                if (!$hasPerm) continue;

                $anonymousEntry = LktMenuEntry::getInstance()
                    ->setType(MenuEntryType::WebItems->value)
                    ->setComponent($webItem->publicComponentName ?? $webItem->component)
                ;

                $group = $webItem->getMenuGroup();
                if ($group) {
                    if (!$groups[$group]) {

                        $text = Translations::get("webItems.{$group}");
                        if (!$text) $text = $group;

                        $r[] = LktMenuEntry::getInstance()
                            ->setName($text)
                            ->setType(MenuEntryType::Parent->value)
                            ->setAccessPolicy('r-app-menu')->autoRead();
                        $groups[$group] = $i;
                    }
                    $r[$groups[$group]]['children'][] = $anonymousEntry->setAccessPolicy('r-app-menu')->autoRead();

                } else {
                    $r[] = $anonymousEntry->setAccessPolicy('r-app-menu')->autoRead();
                }

                ++$i;
            }
        }
        return $r;
    }
}