<?php

namespace Lkt\Factory\Instantiator\Instances;

use Lkt\Factory\Instance\Interfaces\Item;
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
use Lkt\Factory\Instance\Traits\ItemWithForeignKeysDataTrait;
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
use Lkt\Factory\Instance\Traits\ItemWithStringDataTrait;
use Lkt\Factory\Instantiator\Exceptions\UnsetFieldStorePathException;
use Lkt\Factory\Instantiator\Helpers\QueryBuilderHelper;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnBooleanTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnColorTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnCompositionTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnConcatTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnConstantValueTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnDateTimeTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnEmailTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnEncryptTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnFileTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnFloatTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnForeignListTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnForeignTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnIntegerChoiceTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnIntegerTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnJsonTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnPivotTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnRelatedKeysMergeTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnRelatedKeysTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnRelatedTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnStringChoiceTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnStringTrait;
use Lkt\Factory\Instantiator\Instances\AccessDataTraits\ColumnValueListTrait;
use Lkt\Factory\Instantiator\Instantiator;
use Lkt\Factory\Schemas\Enums\AccessPolicyEndOfLife;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\InvalidSchemaAppClassException;
use Lkt\Factory\Schemas\Exceptions\MissedMandatoryValueException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Locale\Locale;
use Lkt\QueryBuilding\Query;
use Lkt\QueryBuilding\Where;

abstract class AbstractInstance implements Item
{
    use ItemWithStringDataTrait,
        ItemWithIntegerDataTrait,
        ItemWithMultipleIntegerDataTrait,
        ItemWithFloatDataTrait,
        ItemWithMultipleFloatDataTrait,
        ItemWithBooleanDataTrait,
        ItemWithDateDataTrait,
        ItemWithColorDataTrait,
        ItemWithEncryptDataTrait,
        ItemWithForeignKeyDataTrait,
        ItemWithForeignKeysDataTrait,
        ItemWithRelatedItemDataTrait,
        ItemWithRelatedItemsDataTrait,
        ItemWithPivotDataTrait,
        ItemWithComposedDataTrait,
        ItemWithFileDataTrait,
        ItemWithJSONDataTrait,
        ItemWithConstantDataTrait,
        ItemWithConcatDataTrait,
        ItemWithMultipleStringDataTrait;

    // @todo these traits must be automatically generated in order to remove AbstractInstance extension
    use ItemWithIdentifierValueTrait,
        ItemWithDataTrait,
        ItemWithAccessPolicyTrait,
        ItemWithInstanceFactoryTrait,
        ItemWithCrudTrait,
        ItemWithSchemaStorePathTrait;

    use ColumnStringTrait,
        ColumnIntegerTrait,
        ColumnFloatTrait,
        ColumnEmailTrait,
        ColumnBooleanTrait,
        ColumnColorTrait,
        ColumnJsonTrait,
        ColumnFileTrait,
        ColumnForeignTrait,
        ColumnForeignListTrait,
        ColumnRelatedTrait,
        ColumnRelatedKeysTrait,
        ColumnPivotTrait,
        ColumnDateTimeTrait,
        ColumnStringChoiceTrait,
        ColumnIntegerChoiceTrait,
        ColumnEncryptTrait,
        ColumnRelatedKeysMergeTrait,
        ColumnValueListTrait,
        ColumnConcatTrait,
        ColumnCompositionTrait,
        ColumnConstantValueTrait;

    protected array $UPDATED_RELATED_DATA = [];
    protected array $PENDING_UPDATE_RELATED_DATA = [];

    const COMPONENT = '';

    /**
     * @param array $initialData
     */
    public function __construct(array $initialData = [])
    {
        $this->initialFeed($initialData);
    }

    /**
     * @return Query
     * @throws SchemaNotDefinedException
     */
    public static function getQueryBuilder()
    {
        return QueryBuilderHelper::getComponentQuery(static::COMPONENT);
    }

    public function getSchema(): Schema|null
    {
        return Schema::get(static::COMPONENT);
    }


    public static function getUniqueFilteredQueryBuilder(array $data): Query
    {
        $schema = Schema::get(static::COMPONENT);

        $query = static::getQueryBuilder();
        $langCodes = Locale::getAvailableLangCodesValues();

        foreach ($schema->getUniqueFields() as $field) {
            if ($field instanceof StringField) {
                if ($field->isI18nJson()) {
                    foreach ($langCodes as $langCode) {
                        if (isset($data[$field->getName()][$langCode])) {
                            $query->andStringEqual($field->getLocaleColumn($langCode), $data[$field->getName()][$langCode]);
                        }
                    }
                } else {
                    $query->andStringEqual($field->getColumn(), $data[$field->getName()]);
                }
            }
        }

        return $query;
    }

    /**
     * @deprecated Use $schema->getWhereBuilder instead
     * @return Where
     */
    public static function getWhereBuilder(): Where
    {
        return Instantiator::getCustomWhere(static::COMPONENT);
    }

    /**
     * @deprecated
     * @throws InvalidComponentException
     * @throws InvalidSchemaAppClassException
     * @throws SchemaNotDefinedException
     */
    public function convertToComponent(string $component = ''): ?static
    {
        return Instantiator::make($component, $this->getIdColumnValue());
    }

    /**
     * @deprecated
     * @return Query
     * @throws SchemaNotDefinedException
     */
    public static function getQueryCaller()
    {
        return QueryBuilderHelper::getComponentQuery(static::COMPONENT);
    }

    /**
     * @deprecated by feedAndSave (which automatically checks is has to create or not)
     *
     * @param array $data
     * @param array $internalMethodsArguments
     * @return $this
     * @throws MissedMandatoryValueException
     * @throws UnsetFieldStorePathException
     */
    public function autoCreate(array $data, array $internalMethodsArguments = []): static
    {
        return $this->feedAndSave($data, $internalMethodsArguments);
//        static::feedInstance($this, $this->prepareCrudData($data, CrudOperation::Create), $internalMethodsArguments);
//        return $this->save();
    }

    /**
     * @deprecated by feedAndSave (which automatically checks is has to create or not)
     *
     * @param array $data
     * @param array $internalMethodsArguments
     * @return $this
     * @throws MissedMandatoryValueException
     * @throws UnsetFieldStorePathException
     */
    public function autoUpdate(array $data, array $internalMethodsArguments = []): static
    {
        return $this->feedAndSave($data, $internalMethodsArguments);
//        static::feedInstance($this, $this->prepareCrudData($data, CrudOperation::Update), $internalMethodsArguments);
//        return $this->save();
    }

    /**
     * @deprecated
     * @param array $params
     * @return static
     */
    public static function create(array $params): static
    {
        return (new static())->feedAndSave($params);
    }

    /**
     * @deprecated
     * @param AbstractInstance $instance
     * @param array $params
     * @return static
     */
    public static function update(AbstractInstance $instance, array $params): static
    {
        return $instance->feedAndSave($params);
    }

    /**
     * @deprecated
     * @param AbstractInstance $instance
     * @param array $params
     * @param array $internalMethodsArguments
     * @return static
     */
    public static function feedInstance(AbstractInstance $instance, array $params, array $internalMethodsArguments = []): static
    {
        return $instance->feed($params, $internalMethodsArguments);
    }

    /**
     * @deprecated
     * @param AbstractField[] $fields
     * @return array
     */
    public function readAsRelated(array $internalMethodsArguments = []): array
    {
        $schema = Schema::get(static::COMPONENT);
        if ($this->accessPolicy) {
            $fields = $schema->getAccessPolicyFields($this->accessPolicy);
            $composedFields = $schema->getAccessPolicyComposedFields($this->accessPolicy);

        } else if ($schema->hasRelatedAccessPolicy()) {
            $fields = $schema->getAccessPolicyFields('lkt-related');
            $composedFields = $schema->getAccessPolicyComposedFields('lkt-related');
            $this->setAccessPolicy('lkt-related', AccessPolicyEndOfLife::UntilNextRead);

        } else {
            $fields = $schema->getSameTableFields();
            $composedFields = $schema->getComposedFields();
        }

        $fieldsStack = [...$fields, ...$composedFields];

        $r = $this->patchReadData($this->readFields($fieldsStack, $internalMethodsArguments));

        if (isset($this->accessPolicy) && $this->accessPolicy->matchedEndOfLife(AccessPolicyEndOfLife::UntilNextRead)) {
            unset($this->accessPolicy);
        }

        return $r;
    }
}