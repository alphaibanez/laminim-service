<?php

namespace Lkt\CodeMaker\FieldGeneration;

use Lkt\CodeMaker\Interfaces\FieldGenerator;
use Lkt\CodeMaker\Traits\FieldGeneratorCommon;
use Lkt\Factory\Instance\Traits\ItemWithRelatedItemsDataTrait;
use Lkt\Factory\Schemas\Fields\AbstractField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;

class RelatedKeysMergeFieldGenerator implements FieldGenerator
{
    use FieldGeneratorCommon;

    public function getGetters(): string
    {
        /** @var RelatedField|ForeignKeysField|RelatedKeysField $field */
        $field = $this->data->field;

        $r = [];

        $returnAnnotation = $this->getRelatedReturnAnnotationFormatted();
        $additionalInputDetection = "\$additionalData = [{$this->data->additionalInputDetection}]; ";

        if ($returnAnnotation) $r[] = "/** {$returnAnnotation}[] */";

        $r[] = "public function get{$this->data->methodName}(Where|null \$where = null, int|null \$page = null, int|null \$itemsPerPage = null, bool \$forceRefresh = false): array|null { {$additionalInputDetection}return \$this->relatedItemsData->getItems('{$this->data->fieldName}', \$where, \$page, \$itemsPerPage, \$additionalData); }";
        $r[] = "public function getRaw{$this->data->methodName}(Where|null \$where = null, int|null \$page = null, int|null \$itemsPerPage = null, bool \$forceRefresh = false): array|null { {$additionalInputDetection}return \$this->relatedItemsData->getItems('{$this->data->fieldName}', \$where, \$page, \$itemsPerPage, \$additionalData, true); }";

        $r[] = "public function get{$this->data->methodName}Page(int|null \$page, Where|null \$where = null, int|null \$itemsPerPage = null, array \$additionalData = [], bool \$forceRefresh = false): array|null { return \$this->relatedItemsData->getItems('{$this->data->fieldName}', \$where, \$page, \$itemsPerPage, \$additionalData, \$forceRefresh); }";
        $r[] = "public function getRaw{$this->data->methodName}Page(int|null \$page, Where|null \$where = null, int|null \$itemsPerPage = null, array \$additionalData = [], bool \$forceRefresh = false): array|null { return \$this->relatedItemsData->getItems('{$this->data->fieldName}', \$where, \$page, \$itemsPerPage, \$additionalData, \$forceRefresh, true); }";

        $r[] = "public function get{$this->data->methodName}Count(string|null \$countableField = null, Where|null \$where = null): int|null { return \$this->relatedItemsData->getItemsCount('{$this->data->fieldName}', \$where, \$countableField); }";
//        $r[] = "public function getRaw{$this->data->methodName}Count(string|null \$countableField = null, Where|null \$where = null): int|null { return \$this->relatedItemsData->getItemsCount('{$this->data->fieldName}', \$where, \$countableField, true); }";

        return implode(' ', $r);
    }

    public function getSetters(): string
    {
        return '';
    }

    public function getCheckers(): string
    {
        $r = [];

        $r[] = "public function has{$this->data->methodName}(Where|null \$where = null, int|null \$page = null, int|null \$itemsPerPage = null, array \$additionalData = [], bool \$forceRefresh = false): bool { return \$this->relatedItemsData->has('{$this->data->fieldName}', \$where, \$page, \$itemsPerPage, \$additionalData, \$forceRefresh); }";
//        $r[] = "public function hasRaw{$this->data->methodName}(Where|null \$where = null, int|null \$page = null, int|null \$itemsPerPage = null, array \$additionalData = [], bool \$forceRefresh = false): bool { return \$this->relatedItemsData->has('{$this->data->fieldName}', \$where, \$page, \$itemsPerPage, \$additionalData, \$forceRefresh, true); }";

        return implode(' ', $r);
    }

    public function parse(): string
    {
        return implode(' ', [
            $this->getGetters(),
            $this->getCheckers(),
        ]);
    }

    public static function generateTraitsUsageCode(AbstractField $field): array
    {
        return [
            ItemWithRelatedItemsDataTrait::class
        ];
    }
}