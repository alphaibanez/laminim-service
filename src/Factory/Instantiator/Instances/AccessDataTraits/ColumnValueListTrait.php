<?php

namespace Lkt\Factory\Instantiator\Instances\AccessDataTraits;

trait ColumnValueListTrait
{
    protected function _getValueListValArray(string $fieldName): array
    {
        return $this->multipleStringData->get($fieldName);
//        $data = $this->_getStringVal($fieldName);
//        if (!$data) return [];
//
//        $schema = Schema::get(static::COMPONENT);
//        $field = $schema->getField($fieldName);
//        $separator = $field->getSeparator();
//        return explode($separator, $data);
    }

    protected function _setValueListVal(string $fieldName, string|array $value = null): static
    {
        $this->multipleStringData->set($fieldName, $value);
        return $this;

//        $schema = Schema::get(static::COMPONENT);
//        $field = $schema->getField($fieldName);
//        $separator = $field->getSeparator();
//
//        if (!is_array($value)) $value = explode($separator, $value);
//
//        $value = array_unique($value, SORT_REGULAR);
//
//        $value = implode($separator, $value);
//
//        $converter = new RawResultsToInstanceConverter(static::COMPONENT, [
//            $fieldName => $value,
//        ], false);
//
//        foreach ($converter->parse() as $key => $value) {
//            $this->UPDATED[$key] = $value;
//        }
//        return $this;
    }
}