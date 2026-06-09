<?php

namespace Lkt\Instances;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Generated\GeneratedLktShoppingPrice;
use Lkt\Traits\WithComponentIdTrait;

class LktShoppingPrice extends GeneratedLktShoppingPrice
{
    const COMPONENT = 'lkt-shopping-price';

    use WithComponentIdTrait;

    public function prepareCrudData(array $data, CrudOperation|null $operation = null): array
    {
        if (isset($data['webItemName'])) {
            $data['componentId'] = $this->getComponentIdByWebItemName($data['webItemName']);
        }

        return $data;
    }
}