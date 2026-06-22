<?php

namespace Lkt\Factory\Instance\DTO;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ColorField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\EncryptField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\IntegerField;
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
    public array $unixTimeStampData;
    public array $dateData;
    public array $colorData;
    public array $encryptData;
    public array $foreignKeyData;

    public function __construct(Schema $schema, array $data)
    {
        $booleanData = [];
        $stringData = [];
        $emailData = [];
        $integerData = [];
        $multipleIntegerData = [];
        $floatData = [];
        $multipleFloatData = [];
        $unixTimeStampData = [];
        $dateData = [];
        $colorData = [];
        $encryptData = [];
        $foreignKeyData = [];

        foreach ($schema->getAllFields() as $field) {
            $k = $field->getName();

            if ($field instanceof EmailField) {
                $emailData[$k] = $data[$k];
            }
            elseif ($field instanceof StringField) {
                $stringData[$k] = $data[$k];
            }
            elseif ($field instanceof ForeignKeyField) {
                $k1 = "{$k}Id";
                $foreignKeyData[$k] = $data[$k1];
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
            elseif ($field instanceof UnixTimeStampField) {
                $unixTimeStampData[$k] = $data[$k];
            }
            elseif ($field instanceof DateTimeField) {
                $dateData[$k] = $data[$k];
            }
            elseif ($field instanceof ColorField) {
                $colorData[$k] = $data[$k];
            }
            elseif ($field instanceof EncryptField) {
                $encryptData[$k] = $data[$k];
            }
        }

        $this->booleanData = $booleanData;
        $this->emailData = $emailData;
        $this->stringData = $stringData;
        $this->integerData = $integerData;
        $this->multipleIntegerData = $multipleIntegerData;
        $this->floatData = $floatData;
        $this->multipleFloatData = $multipleFloatData;
        $this->unixTimeStampData = $unixTimeStampData;
        $this->dateData = $dateData;
        $this->colorData = $colorData;
        $this->encryptData = $encryptData;
        $this->foreignKeyData = $foreignKeyData;
    }
}