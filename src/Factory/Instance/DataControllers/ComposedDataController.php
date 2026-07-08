<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Enums\InvalidDataMode;
use Lkt\Factory\Instance\Enums\RetrieveDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\ComponentId;
use Lkt\Factory\Instantiator\Enums\CrudOperation;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Schema;

final class ComposedDataController
{
    private array $data = [];
    private array $payload = [];
    private array $additionalData = [];

    private Schema $schema;
    private Item $item;

    public function __construct(Schema &$schema, Item &$ins)
    {
        $this->schema = $schema;
        $this->item = $ins;
    }

    public function getItem(string $key, array $additionalData = []): Item|null
    {
        return $this->item->retrieveValue($key, $additionalData, RetrieveDataMode::Item);

        VarDumper::die($key, $additionalData, $this->schema->getField($key));



        $this->additionalData[$key] = $additionalData;


        $compositionField = $this->schema->getCompositionField($key);
        $composedSchema = Schema::get($compositionField->getComponent($this->schema, $this->item));
        $identifierValue = $this->item->getIdentifierValue();

        if ($compositionField instanceof ForeignKeyField) {
            $getter = $compositionField->getGetterForData();
        } else {
            $getter = $compositionField->getGetterForPrimitiveValue();
        }

        if (!is_callable([$this, $getter])) {
            $this->data[$key] = null;
            return null;
        }

        $additionalData = $this->_getCompositionAdditionalData($additionalData, $composedComponent, $this, $getter);

        if (count($additionalData) > 0) {
            $composedInstance = call_user_func_array([$this, $getter], $additionalData);

        } else {

            if ($compositionField instanceof ForeignKeyField) {
                $composedInstance = $this->foreignKeyData->getItem($compositionField->getName());
            } else {
                $composedInstance = $this->relatedItemData->getItem($compositionField->getName());
            }
        }


        if (is_array($composedInstance)) {
            if (count($composedInstance) > 0) $composedInstance = $composedInstance[0];
            else  $composedInstance = null;
        }

        if ($composedInstance === null) {
            $appClass = $composedSchema->getInstanceSettings()->getAppClass();
            /** @var AbstractInstance $emptyInstance */
            $emptyInstance = $appClass::getInstance();
            $emptyInstance::feedInstance($emptyInstance, $emptyInstance->prepareCrudData($additionalData, CrudOperation::Create));

            foreach ($composedSchema->getIdentifiers() as $identifier) {
                if (isset($additionalData[$identifier->getName()])) {
                    if ($additionalData[$identifier->getName()] instanceof AbstractInstance) {
                        $setter = $identifier->getSetterForPrimitiveValue();
                        $emptyInstance->{$setter}((int)$additionalData[$identifier->getName()]?->getIdColumnValue());

                    } elseif($identifier instanceof ForeignKeyField) {
                        $setter = $identifier->getSetterForPrimitiveValue();
                        $content = (int)$additionalData[$identifier->getName()] instanceof AbstractInstance ? $additionalData[$identifier->getName()]?->getIdColumnValue() : $additionalData[$identifier->getName()];
                        $emptyInstance->{$setter}($content);

                    } else {
                        $setter = $identifier->getSetter();
                        $emptyInstance->{$setter}($additionalData[$identifier->getName()]);
                    }
                } elseif (method_exists($identifier, 'getComponent') && $identifier?->getComponent() === static::COMPONENT) {
//                    $setter = $identifier->getSetterForPrimitiveValue();
                    foreach ($identifierValue as $k => $v) {
                        $emptyInstance->assignValue($identifier->getName(), $v);
                    }
//                    $emptyInstance->{$setter}((int)$this->getIdColumnValue());
                }
            }

            if (!$this->isAnonymous()) {
                $feedColum = $compositionField->getColumn();
                $feedField = $composedSchema->getField($feedColum);
                if ($feedField) {
                    $setter = $feedField->getSetterForPrimitiveValue();
                    if (method_exists($emptyInstance, $setter)) {
                        foreach ($identifierValue as $k => $v) {
                            $emptyInstance->assignValue($feedField->getName(), $v);
                        }
                    }
                }
            }

            $backPointerField = $composedSchema->getOneFieldPointingToComponent(static::COMPONENT);

            if ($backPointerField) {
                $setter = $backPointerField?->getSetterForPrimitiveValue();
                if ($setter) $emptyInstance->{$setter}((int)$this?->getIdColumnValue());
            }

            $composedInstance = $emptyInstance;
        }



    }

    public function has(string $key): bool
    {
        $v = $this->getItem($key);

        $f = $this->schema->getForeignKeyField($key);
        $mode = $f->getEmptyDataMode();

        if ($mode === EmptyDataMode::OnlyNull) return $v !== null;
        return $v > 0;
    }

    public function __debugInfo() {
        return [
            'data' => $this->data,
            'payload' => $this->payload,
        ];
    }
}