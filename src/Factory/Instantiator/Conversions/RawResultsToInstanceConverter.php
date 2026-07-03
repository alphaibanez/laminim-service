<?php

namespace Lkt\Factory\Instantiator\Conversions;

use Lkt\Factory\Instantiator\Validations\ParseFieldValue;
use Lkt\Factory\Instantiator\Validations\ValidateFieldValue;
use Lkt\Factory\Schemas\Exceptions\InvalidComponentException;
use Lkt\Factory\Schemas\Exceptions\SchemaNotDefinedException;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Factory\Schemas\Values\ComponentValue;

/**
 * @deprecated
 */
final class RawResultsToInstanceConverter
{
    protected $component;
    protected $data;
    protected $schema;
    protected $allFields = true;
    protected $instance;

    /**
     * @param string $component
     * @param array $data
     * @param bool $allFields
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    public function __construct(string $component, array $data, bool $allFields = true, $instance = null)
    {
        $this->component = new ComponentValue($component);
        $this->schema = Schema::get($this->component->getValue());
        $this->allFields = $allFields;
        $this->data = $data;
        $this->instance = $instance;
    }

    /**
     * @return array
     * @throws InvalidComponentException
     */
    final public function parse(): array
    {
        $r = $this->parseData();
        $data = $this->checkData($r);
        foreach ($data as $datum => $val) $r[$datum] = $val;
        return $r;
    }

    /**
     * @return array
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    private function parseData(): array
    {
        $fields = $this->schema->getAllFields();
        $data = $this->data;
        $allFields = $this->allFields;
        $result = [];

        return array_reduce($fields, function ($result, AbstractField $field) use ($data, $allFields) {
            $searchKey = $field->getName();
            $storeKey = $field->getName();

            if ($field instanceof ForeignKeyField) {
                $storeKey .= 'Id';

                // Fix: parse foreign key integer datum while updating data
                if (!array_key_exists($searchKey, $data) && array_key_exists($storeKey, $data) ) {
                    $searchKey = $storeKey;
                }
            }

            // Use array_key_exists over isset because if value is null, isset returns a false positive
            $originalValue = array_key_exists($searchKey, $data) ? $data[$searchKey] : null;
            $value = ParseFieldValue::parse($field, $originalValue, $this->instance);

            if ($allFields || array_key_exists($searchKey, $data)) {
                $result[$storeKey] = $value;

                if ($field instanceof FileField) {
                    $result["{$storeKey}Name"] = $originalValue;
                }
            }
            return $result;
        }, $result);
    }

    /**
     * @param array $data
     * @return array
     * @throws InvalidComponentException
     * @throws SchemaNotDefinedException
     */
    private function checkData(array $data = []): array
    {
        $fields = $this->schema->getAllFields();
        $allFields = $this->allFields;
        $result = [];

        return array_reduce($fields, function ($result, $field) use ($data, $allFields) {

            $name = $field->getName();
            $value = isset($data[$name]) ? $data[$name] : null;
            $status = ValidateFieldValue::validate($field, $value);
            $key = trim('has' . ucfirst($name));
            if ($allFields || isset($data[$name])) {
                $result[$key] = $status;
            }
            return $result;
        }, $result);
    }
}