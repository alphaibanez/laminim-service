<?php

namespace Lkt\Factory\Instance\DTO;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ColorField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\EncryptField;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Fields\RelatedKeysMergeField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Fields\UnixTimeStampField;
use Lkt\Factory\Schemas\Schema;

final readonly class GroupedData
{
    public array $booleanData;
    public array $stringData;
    public array $integerData;
    public array $multipleIntegerData;
    public array $floatData;
    public array $multipleFloatData;
    public array $dateData;
    public array $colorData;
    public array $encryptData;
    public array $foreignKeyData;
    public array $foreignKeysData;
    public array $relatedItemData;
    public array $relatedItemsData;
    public array $jsonData;
    public array $fileData;
    public array $multipleFileData;

    public function __construct(Schema $schema, array $data)
    {
        $booleanData = [];
        $stringData = [];
        $integerData = [];
        $multipleIntegerData = [];
        $floatData = [];
        $multipleFloatData = [];
        $dateData = [];
        $colorData = [];
        $encryptData = [];
        $foreignKeyData = [];
        $foreignKeysData = [];
        $relatedItemData = [];
        $relatedItemsData = [];
        $jsonData = [];
        $fileData = [];
        $multipleFileData = [];

        foreach ($schema->getAllFields() as $field) {
            $k = $field->getName();
            $dataKey = $k;
            if ($field instanceof ForeignKeyField) {
                if (!array_key_exists($dataKey, $data)) {
                    $dataKey = "{$k}Id";
                }
            } elseif ($field instanceof ForeignKeysField) {
                if (!array_key_exists($dataKey, $data)) {
                    $dataKey = "{$k}Ids";
                }
            }

            $propIsDefined = array_key_exists($dataKey, $data);
            if (!$propIsDefined) continue;

            if ($field instanceof StringField || $field instanceof EmailField) {
                $stringData[$k] = $data[$dataKey];
            }
            elseif ($field instanceof ForeignKeyField) {
                $foreignKeyData[$k] = $data[$dataKey];
            }
            elseif ($field instanceof ForeignKeysField) {
                $foreignKeysData[$k] = $data[$dataKey];
            }
            elseif ($field instanceof IntegerField) {
                if ($field->isMultiple()) {
                    $multipleIntegerData[$k] = $data[$dataKey];
                } else {
                    $integerData[$k] = $data[$dataKey];
                }
            }
            elseif ($field instanceof FloatField) {
                if ($field->isMultiple()) {
                    $multipleFloatData[$k] = $data[$dataKey];
                } else {
                    $floatData[$k] = $data[$dataKey];
                }
            }
            elseif ($field instanceof BooleanField) {
                $booleanData[$k] = $data[$dataKey];
            }
            elseif ($field instanceof DateTimeField || $field instanceof UnixTimeStampField) {
                $dateData[$k] = $data[$dataKey];
            }
            elseif ($field instanceof ColorField) {
                $colorData[$k] = $data[$dataKey];
            }
            elseif ($field instanceof EncryptField) {
                $encryptData[$k] = $data[$dataKey];
            }
            elseif ($field instanceof JSONField) {
                $jsonData[$k] = $data[$dataKey];
            }
            elseif ($field instanceof FileField) {
                if ($field->isMultiple()) {
                    $multipleFileData[$k] = $data[$dataKey];
                } else {
                    $fileData[$k] = $data[$dataKey];
                }
            }

            elseif ($field instanceof RelatedKeysField) {
                $relatedItemsData[$k] = $data[$dataKey];
            }

            elseif ($field instanceof RelatedKeysMergeField) {
                $relatedItemsData[$k] = $data[$dataKey];
            }

            // This should never occur since it's remote data
            elseif ($field instanceof RelatedField) {
                if ($field->isSingleMode()) {
                    $relatedItemData[$k] = $data[$dataKey];
                } else {
                    $relatedItemsData[$k] = $data[$dataKey];
                }
            }
        }

        $this->booleanData = $booleanData;
        $this->stringData = $stringData;
        $this->integerData = $integerData;
        $this->multipleIntegerData = $multipleIntegerData;
        $this->floatData = $floatData;
        $this->multipleFloatData = $multipleFloatData;
        $this->dateData = $dateData;
        $this->colorData = $colorData;
        $this->encryptData = $encryptData;
        $this->foreignKeyData = $foreignKeyData;
        $this->foreignKeysData = $foreignKeysData;
        $this->relatedItemData = $relatedItemData;
        $this->relatedItemsData = $relatedItemsData;
        $this->jsonData = $jsonData;
        $this->fileData = $fileData;
        $this->multipleFileData = $multipleFileData;
    }
}