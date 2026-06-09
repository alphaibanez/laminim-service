<?php

namespace Lkt\Traits;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instantiator\ComponentId;
use Lkt\WebItems\WebItem;

trait WithComponentIdTrait
{

    public function getComponentIdAssociatedComponent(): string
    {
        return ComponentId::getComponent($this->getComponentId());
    }

    public function getComponentIdAssociatedWebItem(): WebItem
    {
        return WebItem::detectWebItem($this->getComponentIdAssociatedComponent());
    }

    public function getComponentIdAssociatedWebItemPublicName(): string
    {
        return $this->getComponentIdAssociatedWebItem()->publicComponentName;
    }

    public function getComponentIdByWebItemName(string $publicName): int
    {
        $webItem = WebItem::detectWebItem($publicName);
        return ComponentId::getId($webItem->component);
    }
}