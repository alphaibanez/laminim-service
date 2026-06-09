<?php

namespace Lkt\Instances;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Generated\GeneratedLktShoppingSubscription;
use Lkt\Traits\WithComponentIdTrait;

class LktShoppingSubscription extends GeneratedLktShoppingSubscription
{
    const COMPONENT = 'lkt-shopping-subscription';

    use WithComponentIdTrait;

    public function prepareCrudData(array $data, CrudOperation|null $operation = null): array
    {
        if (isset($data['webItemName'])) {
            $data['componentId'] = $this->getComponentIdByWebItemName($data['webItemName']);
        }
        return $data;
    }
}