<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Schema;

trait ColumnCompositionTrait
{
    protected array $COMPOSED_DATA = [];

    protected function _getCompositionAdditionalData(array $additionalData = [], string $fieldName = null, mixed $reflectedInstance = null, string $reflectedMethod = null)
    {
        return $this->composedData->prepareAdditionalData((string)$fieldName, $additionalData);
    }

    protected function _feedAnonymousComposedInstance(AbstractInstance $instance): AbstractInstance
    {
        $composedSchema = Schema::get($instance::COMPONENT);
        $remoteIdentifierPointingToMe = $composedSchema->getOneFieldPointingToComponent(static::COMPONENT);

        if ($remoteIdentifierPointingToMe) {
            $setter = $remoteIdentifierPointingToMe->getSetterForPrimitiveValue();
            $instance->{$setter}((int)$this?->getIdColumnValue());
        }

        return $instance;
    }

    protected function _getCompositionInstance(string $composedComponent, array $additionalData = []): mixed
    {
        return $this->composedData->getItem($composedComponent, $additionalData);
    }

    /**
     * @param string $composedComponent
     * @param string $fieldName
     * @return mixed
     * @throws SchemaNotDefinedException
     */
    protected function _getCompositionVal(string $composedComponent, string $fieldName, array $additionalData = []): mixed
    {
        $ins = $this->composedData->getItem($composedComponent, $additionalData);
        if (!$ins) return null;

        return $ins->retrieveValue($fieldName, $additionalData);
    }

    /**
     * @param string $component
     * @param string $composedComponent
     * @param string $fieldName
     * @param mixed $value
     * @return $this
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _setCompositionVal(string $composedComponent, string $fieldName, mixed $value, array $additionalData = []): static
    {
        $ins = $this->composedData->getItem($composedComponent, $additionalData);
        if (!$ins) return $this;

        $ins->assignValue($fieldName, $value);
        return $this;
    }

    /**
     * @param string $composedComponent
     * @param string $fieldName
     * @return bool
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    protected function _hasCompositionVal(string $composedComponent, string $fieldName, array $additionalData = []): bool
    {
        $ins = $this->composedData->getItem($composedComponent, $additionalData);
        if (!$ins) return false;

        return $ins->hasAssignedValue($fieldName);
    }

    protected function _saveCompositionValues(bool $isUpdate = false)
    {
        $schema = Schema::get(static::COMPONENT);

        foreach ($this->COMPOSED_DATA as $fieldName => $composedInstance) {

            if (!$isUpdate){
                $this->_feedAnonymousComposedInstance($composedInstance);
            }

            $relatedAccessPolicy = null;
            if (isset($this->accessPolicy) && $this->accessPolicy) {
                $field = $schema->getCompositionField($fieldName);
                $relatedAccessPolicy = $schema->getAccessPolicyForRelationalField($this->accessPolicy, $field);
            }

            if (is_object($composedInstance) && is_callable([$composedInstance, 'save'])) {
                if ($relatedAccessPolicy) $composedInstance->setAccessPolicy($relatedAccessPolicy, AccessPolicyEndOfLife::UntilNextWrite);
                $composedInstance->save();
            }
        }
    }
}