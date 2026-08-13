<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Instance\Traits\ItemWithForeignKeysDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemDataTrait;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemsDataTrait;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;

class RelatedFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        /** @var RelatedField|ForeignKeysField|RelatedKeysField $field */
        $field = $this->data->field;

        $r = [];

        $returnAnnotation = $this->getRelatedReturnAnnotationFormatted();
        $additionalInputDetection = "\$additionalData = [{$this->data->additionalInputDetection}]; ";

        if ($field instanceof RelatedField && $field->isSingleMode()) {
            if ($returnAnnotation) {
                $r[] = "/** {$returnAnnotation} */";
            }
            $r[] = "public function get{$this->data->methodName}({$this->data->additionalInput}){$this->getRelatedReturnTypeFormatted()} { {$additionalInputDetection}return \$this->relatedItemData->getItem('{$this->data->fieldName}', \$additionalData); }";

        } elseif ($field instanceof RelatedField || $field instanceof RelatedKeysField) {

            if ($returnAnnotation) $r[] = "/** {$returnAnnotation}[] */";

            $additionalInput = $this->data->additionalInput;
            if ($additionalInput !== '') $additionalInput = ", {$additionalInput}";
            $r[] = "public function get{$this->data->methodName}(Where|null \$where = null, int|null \$page = null, int|null \$itemsPerPage = null, bool \$forceRefresh = false {$additionalInput}): array|null { {$additionalInputDetection}return \$this->relatedItemsData->getItems('{$this->data->fieldName}', \$where, \$page, \$itemsPerPage, \$additionalData); }";

            $r[] = "public function get{$this->data->methodName}Page(int|null \$page, Where|null \$where = null, int|null \$itemsPerPage = null, array \$additionalData = [], bool \$forceRefresh = false): array|null { return \$this->relatedItemsData->getItems('{$this->data->fieldName}', \$where, \$page, \$itemsPerPage, \$additionalData, \$forceRefresh); }";
            $r[] = "public function get{$this->data->methodName}Count(string|null \$countableField = null, Where|null \$where = null): int|null { return \$this->relatedItemsData->getItemsCount('{$this->data->fieldName}', \$where, \$countableField); }";
            $r[] = "public function get{$this->data->methodName}AmountOfPages(string|null \$countableField = null, Where|null \$where = null, int|null \$itemsPerPage = null): int|null { return \$this->relatedItemsData->getItemsAmountOfPages('{$this->data->fieldName}', \$where, \$countableField, \$itemsPerPage); }";

        } elseif ($field instanceof ForeignKeysField) {

            $r[] = "public function get{$this->data->methodName}(): string|null { return \$this->foreignKeysData->get('{$this->data->fieldName}'); }";
            if ($returnAnnotation) $r[] = "/** {$returnAnnotation}[] */";
            $r[] = "public function get{$this->data->methodName}Data(): array|null { return \$this->foreignKeysData->getItems('{$this->data->fieldName}'); }";
            $r[] = "public function get{$this->data->methodName}Ids(): array|null { return \$this->foreignKeysData->getIds('{$this->data->fieldName}'); }";
        }

        return implode(' ', $r);
    }

    public function getSetters(): string
    {
        /** @var RelatedField|ForeignKeysField|RelatedKeysField $field */
        $field = $this->data->field;

        $r = [];

        if ($field instanceof RelatedField && $field->isSingleMode()) {
            $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
            $r[] = "public function set{$this->data->methodName}WithData(array \${$this->data->fieldName}):static { \$this->relatedItemData->setItem('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        } elseif ($field instanceof RelatedField) {

            $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
            $r[] = "public function set{$this->data->methodName}WithData(array \${$this->data->fieldName}):static { \$this->relatedItemsData->setItems('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";

        } elseif ($field instanceof ForeignKeysField) {
            $r[] = "/** @return {$this->data->selfReturningAnnotation} */";
            $r[] = "public function set{$this->data->methodName}(\${$this->data->fieldName}):static { \$this->foreignKeysData->set('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";
            $r[] = "public function remove{$this->data->methodName}Ids(\${$this->data->fieldName}):static { \$this->foreignKeysData->removeIds('{$this->data->fieldName}', \${$this->data->fieldName}); return \$this; }";
        }
        return implode(' ', $r);
    }

    public function getCheckers(): string
    {
        /** @var RelatedField|ForeignKeysField|RelatedKeysField $field */
        $field = $this->data->field;
        $additionalInputDetection = "\$additionalData = [{$this->data->additionalInputDetection}]; ";

        $r = [];

        if ($field instanceof RelatedField && $field->isSingleMode()) {
            $r[] = "public function has{$this->data->methodName}({$this->data->additionalInput}):bool { {$additionalInputDetection}return \$this->relatedItemData->has('{$this->data->fieldName}', \$additionalData); }";

        } elseif ($field instanceof RelatedField || $field instanceof RelatedKeysField) {

            $additionalInput = $this->data->additionalInput;
            if ($additionalInput !== '') $additionalInput = ", {$additionalInput}";
            $r[] = "public function has{$this->data->methodName}(Where|null \$where = null, int|null \$page = null, int|null \$itemsPerPage = null, bool \$forceRefresh = false {$additionalInput}): bool { {$additionalInputDetection}return \$this->relatedItemsData->has('{$this->data->fieldName}', \$where, \$page, \$itemsPerPage, \$additionalData, \$forceRefresh); }";

        } elseif ($field instanceof ForeignKeysField) {
            $r[] = "public function has{$this->data->methodName}(\${$this->data->fieldName}):bool { return \$this->foreignKeysData->has('{$this->data->fieldName}', \${$this->data->fieldName}); }";
        }
        return implode(' ', $r);
    }

    public function parse(): string
    {
        return implode(' ', [
            $this->getGetters(),
            $this->getSetters(),
            $this->getCheckers(),
        ]);
    }

    public static function generateTraitsUsageCode(AbstractField $field): array
    {
        if ($field instanceof ForeignKeysField) {
            return [
                ItemWithForeignKeysDataTrait::class
            ];
        }

        if ($field instanceof RelatedField && $field->isSingleMode()) {
            return [
                ItemWithRelatedItemDataTrait::class
            ];
        }

        return [
            ItemWithRelatedItemsDataTrait::class
        ];
    }
}