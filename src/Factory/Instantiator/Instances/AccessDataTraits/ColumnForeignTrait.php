<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Traits\ItemWithForeignKeyDataTrait;
use Lkt\Factory\Instantiator\ComponentId;
use Lkt\Factory\Instantiator\Helpers\UpdatedRelatedDataProcessor;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Schema;

trait ColumnForeignTrait
{
    use ItemWithForeignKeyDataTrait;

    /**
     * @param string $fieldName
     * @return AbstractInstance|null
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    protected function _getForeignVal($type = '', $id = 0, string $fieldName = ''): ?AbstractInstance
    {
//        return $this->foreignKeyData->getItem($fieldName);

        if ($fieldName !== '') {
            $schema = Schema::get(static::COMPONENT);
            $field = $schema->getField($fieldName);

            if ($field) {
                $type = $field->getComponent();
                $dynamicComponentFieldName = $field->getDynamicComponentField();
                if ($dynamicComponentFieldName !== '') {
                    $dynamicComponentField = $schema->getField($dynamicComponentFieldName);
                    $getter = $dynamicComponentField->getGetterForPrimitiveValue();
                    $dynamicType = $this->{$getter}();
                    if (is_numeric($dynamicType)) $type = ComponentId::getComponent((int)$dynamicType);
                    elseif ($dynamicType !== '') $type = $dynamicType;
                }
                $id = $this->_getIntegerVal($fieldName . 'Id');
            }
        }

        if (!$type || $id <= 0) {
            return null;
        }
        return Instantiator::make($type, $id);
    }

    /**
     * @param string $fieldName
     * @return bool
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    protected function _hasForeignVal($type = '', $id = 0, string $fieldName = ''): bool
    {
//        return $this->foreignKeyData->has($fieldName);

        $schema = Schema::get(static::COMPONENT);
        $field = $schema->getForeignKeyField($fieldName);

        if ($field) {
            $type = $field->getComponent();
            $id = $this->_getIntegerVal($fieldName . 'Id');
        }

        return is_object($this->_getForeignVal($type, $id));
    }
}