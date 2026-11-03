<?php

namespace Lkt\Factory\Instance;

use Lkt\Factory\Instance\Traits\ItemWithAccessPolicyTrait;
use Lkt\Factory\Instance\Traits\ItemWithBooleanDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithColorDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithComposedDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithConcatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithConstantDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithCrudTrait;
use Lkt\Factory\Instance\Traits\ItemWithDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithDateDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithEncryptDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithFileDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithFloatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithForeignKeyDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithIdentifierValueTrait;
use Lkt\Factory\Instance\Traits\ItemWithInstanceFactoryTrait;
use Lkt\Factory\Instance\Traits\ItemWithIntegerDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithJSONDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleFloatDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleIntegerDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithMultipleStringDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithPivotDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemsDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithSchemaStorePathTrait;
use Lkt\Factory\Instance\Traits\ItemWithSchemaTrait;
use Lkt\Factory\Instance\Traits\ItemWithStringDataTrait;

class Item implements \Lkt\Factory\Instance\Interfaces\Item
{
    use ItemWithAccessPolicyTrait,
        ItemWithBooleanDataTrait,
        ItemWithColorDataTrait,
        ItemWithComposedDataTrait,
        ItemWithConcatDataTrait,
        ItemWithConstantDataTrait,
        ItemWithCrudTrait,
        ItemWithDataTrait,
        ItemWithDateDataTrait,
        ItemWithEncryptDataTrait,
        ItemWithFileDataTrait,
        ItemWithFloatDataTrait,
        ItemWithForeignKeyDataTrait,
        ItemWithIdentifierValueTrait,
        ItemWithIntegerDataTrait,
        ItemWithJSONDataTrait,
        ItemWithMultipleFloatDataTrait,
        ItemWithMultipleIntegerDataTrait,
        ItemWithMultipleStringDataTrait,
        ItemWithPivotDataTrait,
        ItemWithRelatedItemDataTrait,
        ItemWithRelatedItemsDataTrait,
        ItemWithSchemaTrait,
        ItemWithSchemaStorePathTrait,
        ItemWithStringDataTrait,
        ItemWithInstanceFactoryTrait;
}