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
    public array $emailData;
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
        $emailData = [];
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
            if ($field instanceof ForeignKeyField) {
                $k = "{$k}Id";
            } elseif ($field instanceof ForeignKeysField) {
                $k = "{$k}Ids";
            }

            $propIsDefined = array_key_exists($k, $data);
//            if (!$propIsDefined) {
//                continue;
//            }

            if ($field instanceof EmailField) {
                $emailData[$k] = $data[$k];
            }
            elseif ($field instanceof StringField) {
                $stringData[$k] = $data[$k];
            }
            elseif ($field instanceof ForeignKeyField) {
                $foreignKeyData[$k] = $data[$k];
            }
            elseif ($field instanceof ForeignKeysField) {
                $foreignKeysData[$k] = $data[$k];
            }
            elseif ($field instanceof IntegerField) {
                if ($field->isMultiple()) {
                    $multipleIntegerData[$k] = $data[$k];
                } else {
                    $integerData[$k] = $data[$k];
                }
            }
            elseif ($field instanceof FloatField) {
                if ($field->isMultiple()) {
                    $multipleFloatData[$k] = $data[$k];
                } else {
                    $floatData[$k] = $data[$k];
                }
            }
            elseif ($field instanceof BooleanField) {
                $booleanData[$k] = $data[$k];
            }
            elseif ($field instanceof DateTimeField || $field instanceof UnixTimeStampField) {
                $dateData[$k] = $data[$k];
            }
            elseif ($field instanceof ColorField) {
                $colorData[$k] = $data[$k];
            }
            elseif ($field instanceof EncryptField) {
                $encryptData[$k] = $data[$k];
            }
            elseif ($field instanceof JSONField) {
                $jsonData[$k] = $data[$k];
            }
            elseif ($field instanceof FileField) {
                if ($field->isMultiple()) {
                    $multipleFileData[$k] = $data[$k];
                } else {
                    $fileData[$k] = $data[$k];
                }
            }

            elseif ($field instanceof RelatedKeysField) {
                $relatedItemsData[$k] = $data[$k];
            }

            elseif ($field instanceof RelatedKeysMergeField) {
                $relatedItemsData[$k] = $data[$k];
            }

            // This should never occur since it's remote data
            elseif ($field instanceof RelatedField) {
                if ($field->isSingleMode()) {
                    $relatedItemData[$k] = $data[$k];
                } else {
                    $relatedItemsData[$k] = $data[$k];
                }
            }
        }

        $this->booleanData = $booleanData;
        $this->emailData = $emailData;
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