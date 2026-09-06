<?php

namespace Lkt\CodeMaker\Helpers;

use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EncryptField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\HTMLField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringChoiceField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Templates\Template;

class FieldsGroupByHelper
{
    public static function makeFieldsCode(Schema $schema, bool $includeStatic = false): string
    {
        $instanceSettings = $schema->getInstanceSettings();

        $className = $instanceSettings?->getQueryCallerFQDN();
        if ($includeStatic) {
            $className = $instanceSettings?->getWhereFQDN();
        }
        $returnSelf = '\\' . $className;

        $methods = [];

        foreach ($schema->getAllFields() as $field) {
            
            $fieldMethod = $field->getName();

            $templateData = [
                'column' => $field->getColumn(),
                'fieldMethod' => $fieldMethod,
                'returnSelf' => $returnSelf,
                'canBeNull' => false,
            ];

            if ($field instanceof ForeignKeyField || $field instanceof IntegerField) {
                $templateData['canBeNull'] =  $field->isNullable();

                if ($field instanceof IntegerField && $field->ableToChoose()) {
                    $templateData['comparatorsIn'] = $field->getComparatorsIn();
                }

                $methods[] = Template::file(__DIR__ . '/../../../assets/phtml/group-by/group-by.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof StringField || $field instanceof HTMLField) {
                $templateData['canBeNull'] =  $field->isNullable();

                if ($field instanceof StringChoiceField) {
                    $options = $field->getAllowedOptions();

                    $optionsMethods = array_map(function ($option) {
                        return str_replace(' ', '', ucwords(str_replace('-', ' ', $option)));
                    }, $options);

                    $templateData['options'] = $options;
                    $templateData['optionsMethods'] = $optionsMethods;
                    $templateData['comparatorsIn'] = $field->getComparatorsIn();
                }

                $methods[] = Template::file(__DIR__ . '/../../../assets/phtml/group-by/group-by.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }

            if ($field instanceof EncryptField) {
                $templateData['canBeNull'] =  $field->isNullable();
                $methods[] = Template::file(__DIR__ . '/../../../assets/phtml/group-by/group-by.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }


            if ($field instanceof FloatField) {
                $templateData['canBeNull'] =  $field->isNullable();
                $methods[] = Template::file(__DIR__ . '/../../../assets/phtml/group-by/group-by.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }


            if ($field instanceof BooleanField) {
                $templateData['canBeNull'] =  $field->isNullable();
                $methods[] = Template::file(__DIR__ . '/../../../assets/phtml/group-by/group-by.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }


            if ($field instanceof ForeignKeysField) {
                $templateData['canBeNull'] =  $field->isNullable();
                $methods[] = Template::file(__DIR__ . '/../../../assets/phtml/group-by/group-by.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }


            if ($field instanceof DateTimeField) {
                $templateData['canBeNull'] =  $field->isNullable();
                $methods[] = Template::file(__DIR__ . '/../../../assets/phtml/group-by/group-by.phtml')
                    ->setData($templateData)
                    ->parse();
                continue;
            }
        }

        return implode("\n", $methods);
    }
}