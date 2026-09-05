<?php

namespace Lkt\CodeMaker\Helpers;

use Lkt\CodeMaker\DTO\FieldGeneratorData;
use Lkt\CodeMaker\FieldGeneration\BooleanFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\ColorFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\ConcatFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\ConstantValueFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\DateTimeFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\EmailFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\EncryptFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\FileFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\FloatFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\ForeignKeyFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\IntegerChoiceFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\IntegerFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\JsonFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\PivotFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\RelatedFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\RelatedKeysMergeFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\StringChoiceFieldGenerator;
use Lkt\CodeMaker\FieldGeneration\StringFieldGenerator;
use Lkt\Factory\Schemas\ComputedFields\BooleansComputedField;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ColorField;
use Lkt\Factory\Schemas\Fields\ConcatField;
use Lkt\Factory\Schemas\Fields\ConstantValueField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\EncryptField;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\HTMLField;
use Lkt\Factory\Schemas\Fields\IntegerChoiceField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Fields\RelatedKeysMergeField;
use Lkt\Factory\Schemas\Fields\StringChoiceField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Fields\UnixTimeStampField;
use Lkt\Factory\Schemas\Fields\ValueListField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Templates\Template;

class FieldsCodeHelper
{
    public static function makeFieldsCode(Schema $schema): array
    {
        $instanceSettings = $schema->getInstanceSettings();

        $className = $instanceSettings?->getAppClass();
        $returnSelf = '\\' . $className;

        $methods = [];
        $traitsUsage = [];

        foreach ($schema->getFields() as $field) {

            $fieldMethod = ucfirst($field->getName());
            $fieldName = $field->getName();

            $fieldGeneratorData = new FieldGeneratorData();
            $fieldGeneratorData->fieldName = $fieldName;
            $fieldGeneratorData->methodName = $fieldMethod;
            $fieldGeneratorData->selfReturningAnnotation = $returnSelf;
            $fieldGeneratorData->field = $field;

            $templateData = [
                'fieldName' => $fieldName,
                'fieldMethod' => $fieldMethod,
                'returnSelf' => $returnSelf,
            ];

            if ($field instanceof ForeignKeyField) {
                $relatedComponent = $field->getComponent();
                $relatedClassName = '';
                if ($relatedComponent) {
                    $relatedSchema = Schema::get($relatedComponent);
                    $relatedClassName = $relatedSchema->getInstanceSettings()->getAppClass();
                }
                $fieldGeneratorData->relatedComponent = $relatedComponent;
                $fieldGeneratorData->relatedReturnType = $relatedClassName;
                $fieldGeneratorData->relatedReturnAnnotation = $relatedClassName;
                $methods[] = ForeignKeyFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = ForeignKeyFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof IntegerChoiceField) {
                $fieldGeneratorData->enabledEmptyPreset = $field->hasEnabledEmptyPreset();
                $fieldGeneratorData->options = $field->getAllowedOptions();
                $fieldGeneratorData->comparatorsIn = $field->getComparatorsIn();
                $fieldGeneratorData->isMultiple = $field->isMultiple();
                $fieldGeneratorData->enumChoiceClass = $field->getEnumChoiceClass();
                $methods[] = IntegerChoiceFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = IntegerChoiceFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof IntegerField) {
                $fieldGeneratorData->isMultiple = $field->isMultiple();
                $methods[] = IntegerFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = IntegerFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof StringChoiceField) {
                $fieldGeneratorData->enabledEmptyPreset = $field->hasEnabledEmptyPreset();
                $fieldGeneratorData->options = $field->getAllowedOptions();
                $fieldGeneratorData->comparatorsIn = $field->getComparatorsIn();
                $fieldGeneratorData->isMultiple = false;
                $fieldGeneratorData->enumChoiceClass = $field->getEnumChoiceClass();
                $methods[] = StringChoiceFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = StringChoiceFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof ValueListField) {
                $fieldGeneratorData->isMultiple = true;
                $methods[] = StringFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = StringFieldGenerator::generateTraitsUsageCode($field);


            } elseif ($field instanceof EmailField) {
                $methods[] = EmailFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = EmailFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof StringField || $field instanceof HTMLField) {
                $methods[] = StringFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = StringFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof EncryptField) {
                $methods[] = EncryptFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = EncryptFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof BooleanField) {
                $methods[] = BooleanFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = BooleanFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof FloatField) {
                $fieldGeneratorData->isMultiple = $field->isMultiple();
                $methods[] = FloatFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = FloatFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof DateTimeField || $field instanceof UnixTimeStampField) {
                $methods[] = DateTimeFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = DateTimeFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof ForeignKeysField || $field instanceof RelatedField || $field instanceof RelatedKeysField) {

                $relatedComponent = $field->getComponent();
                if (Schema::exists($relatedComponent)) {
                    $relatedSchema = Schema::get($relatedComponent);
                    $relatedClassName = $relatedSchema->getInstanceSettings()->getAppClass();
                    $relatedQueryCaller = $relatedSchema->getInstanceSettings()->getQueryCallerFQDN();

                    $fieldGeneratorData->relatedComponent = $relatedComponent;
                    $fieldGeneratorData->relatedReturnAnnotation = $relatedClassName;
                    $fieldGeneratorData->relatedReturnType = $relatedClassName;
                    $fieldGeneratorData->relatedQueryBuilder = $relatedQueryCaller;

                    $templateData['component'] = $relatedComponent;
                    $templateData['relatedClassName'] = ':?\\' . $relatedClassName;
                    $templateData['relatedReturnClass'] = '@return \\' . $relatedClassName . '[]';
                    $templateData['relatedQueryCaller'] = '\Lkt\QueryCaller\QueryCaller';
                    $templateData['singleReturnType'] = '';

                    if ($relatedQueryCaller) {
                        $templateData['relatedQueryCaller'] = '\\' . $relatedQueryCaller;
                    }

                    if ($relatedSchema->hasComplexPrimaryKey()) {
                        $relatedIdentifiers = $relatedSchema->getIdentifiers();
                        $additionalInput = [];
                        $additionalInputDetection = [];
                        foreach ($relatedIdentifiers as $relatedIdentifier) {
                            if ($relatedIdentifier->getColumn() === $field->getColumn()) continue;

                            $relatedIdentifierSchema = Schema::get($relatedIdentifier->getComponent());
                            $relatedIdentifierClassName = $relatedIdentifierSchema->getInstanceSettings()->getAppClass();

                            $additionalInput[] = "\\{$relatedIdentifierClassName}|int|null \${$relatedIdentifier->getName()}";
                            $additionalInputDetection[] = "'{$relatedIdentifier->getName()}' => \${$relatedIdentifier->getName()} instanceOf Item ? (int)\${$relatedIdentifier->getName()}?->getIdColumnValue() : \${$relatedIdentifier->getName()},";
                        }

                        $templateData['additionalInput'] = implode(', ', $additionalInput);
                        $templateData['additionalInputDetection'] = implode(', ', $additionalInputDetection);

                        $fieldGeneratorData->additionalInput = implode(', ', $additionalInput);
                        $fieldGeneratorData->additionalInputDetection = implode(', ', $additionalInputDetection);
                    }
                }

                if ($field->isSoftTyped()) {
                    $templateData['relatedClassName'] = '';
                    $templateData['relatedReturnClass'] = '';
                    $fieldGeneratorData->relatedReturnAnnotation = '';
                    $fieldGeneratorData->relatedReturnType = '';
                }

                $methods[] = RelatedFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = RelatedFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof PivotField) {

                $relatedComponent = $field->getTargetComponent($schema);
                $relatedSchema = Schema::get($relatedComponent);

                $relatedClassName = $relatedSchema->getInstanceSettings()->getAppClass();

                $fieldGeneratorData->relatedComponent = $relatedComponent;
                $fieldGeneratorData->relatedReturnAnnotation = $relatedClassName;
                $fieldGeneratorData->relatedReturnType = $relatedClassName;


                $methods[] = PivotFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = PivotFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof FileField) {
                $fieldGeneratorData->isMultiple = $field->isMultiple();
                $methods[] = FileFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = FileFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof ColorField) {
                $methods[] = ColorFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = ColorFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof ConstantValueField) {
                $fieldGeneratorData->getterReturnType = $field->getConstantValueType();
                $methods[] = ConstantValueFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = ConstantValueFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof JSONField) {
                $methods[] = JsonFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = JsonFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof RelatedKeysMergeField) {
                $methods[] = RelatedKeysMergeFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = RelatedKeysMergeFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof ConcatField) {
                $methods[] = ConcatFieldGenerator::generateCode($fieldGeneratorData);
                $traitsUsage[] = ConcatFieldGenerator::generateTraitsUsageCode($field);

            } elseif ($field instanceof BooleansComputedField) {
                $templateData['allRequired'] = BooleansComputedField::getAllConditionRequiredString($field, $schema);
                if ($templateData['allRequired'] === '') continue;
                $templateData['allRequiredSetter'] = BooleansComputedField::getAllConditionRequiredSetterString($field, $schema);

                $methods[] = Template::file(__DIR__ . '/../../../assets/phtml/computed-fields/booleans-computed-field.phtml')
                    ->setData($templateData)
                    ->parse();

            }
        }

        $finalTraitsUsage = [];
        foreach ($traitsUsage as $traits) {
            foreach ($traits as $t) {
                if (!in_array($t, $finalTraitsUsage, true)) {
                    $finalTraitsUsage[] = $t;
                }
            }
        }

        $traitsUsage = array_unique($finalTraitsUsage);
        sort($traitsUsage);

        foreach ($schema->getCompositionFields() as $compositionField) {
            $compositionFieldName = $compositionField->getName();
            $composedComponent = $compositionField->getComponent();
            $composedSchema = Schema::get($composedComponent);
            $nestedComposedSchema = Schema::get($composedComponent);
            $compositionValues = $compositionField->getCompositionValues();

            foreach ($compositionField->getCompositionContent() as $fieldName => $composedFieldName) {

                $composedPrimitiveInputType = 'mixed';
                $composedPrimitiveReturnType = 'mixed';
                $composedInstanceReturnType = '';
                $composedDocReturn = '';
                $additionalFields = '';
                $additionalInput = '';
                $additionalInputDetection = '';
                $nestedCompositionCalls = [];
                $prepareCompositionDataWithField = $fieldName;

                $nestedCompositionLevel = 1;

                $fieldMethod = ucfirst($fieldName);

                $composedField = $composedSchema->getField($composedFieldName);

                if (!$composedField) {
                    $nestedCompositionField = $composedSchema->getCompositionFieldComposingThisField($composedFieldName);
                    if (!$nestedCompositionField) continue;
                    $nestedComposedSchema = Schema::get($nestedCompositionField->getComponent());
                    $composedField = $nestedComposedSchema->getField($composedFieldName);

                    if (!$composedField) continue;
                    $nestedCompositionCalls[] = "->_getCompositionInstance('$compositionFieldName', \$additionalData)";
                    $compositionFieldName = $nestedCompositionField->getName();
//                    $nestedCompositionCalls[] = "?->_getCompositionVal('{$nestedCompositionField->getName()}', '$fieldName', \$additionalData)";
                    ++$nestedCompositionLevel;

                    $prepareCompositionDataWithField = 'null';
                }


                if ($nestedComposedSchema?->hasComplexPrimaryKey()) {
                    $relatedIdentifiers = $nestedComposedSchema->getIdentifiers();
                    $_additionalInput = [];
                    $_additionalInputDetection = [];
                    foreach ($relatedIdentifiers as $relatedIdentifier) {
                        if ($nestedCompositionLevel === 1 && $relatedIdentifier->getColumn() === $compositionField->getColumn()) continue;

                        $relatedIdentifierSchema = Schema::get($relatedIdentifier->getComponent());
                        $relatedIdentifierClassName = $relatedIdentifierSchema->getInstanceSettings()->getAppClass();

                        $tmpAdditionalInput = "\\{$relatedIdentifierClassName}|int|null \${$relatedIdentifier->getName()}";
                        $compositionValue = $compositionValues[$relatedIdentifier->getName()];
                        if ($compositionValue !== null) {
                            $tmpAdditionalInput .= ' = null';
                        }

                        $_additionalInput[] = $tmpAdditionalInput;
                        $_additionalInputDetection[] = "'{$relatedIdentifier->getName()}' => \${$relatedIdentifier->getName()} instanceOf Item ? (int)\${$relatedIdentifier->getName()}?->getIdColumnValue() : \${$relatedIdentifier->getName()}";
                    }

                    $_additionalInput = array_filter($_additionalInput, function ($d) {
                        return trim($d) !== '';
                    });
                    $_additionalInputDetection = array_filter($_additionalInputDetection, function ($d) {
                        return trim($d) !== '';
                    });

                    $additionalInput = implode(', ', $_additionalInput);
                    $additionalInputDetection = implode(', ', $_additionalInputDetection);
                }

                if ($composedField instanceof ForeignKeyField) {
                    $additionalFields = 'foreign-key';

                } elseif ($composedField instanceof IntegerField) {
                    if ($composedField->isMultiple()) {
                        $composedInstanceReturnType = '@return int[]';
                        $composedPrimitiveReturnType = '?array';
                        $composedPrimitiveInputType = 'array';
                    } else {
                        $composedPrimitiveReturnType = '?int';
                        $composedPrimitiveInputType = 'int';
                    }

                } elseif ($composedField instanceof StringField || $composedField instanceof HTMLField || $composedField instanceof EncryptField || $composedField instanceof ColorField || $composedField instanceof ConcatField) {
                    $composedPrimitiveReturnType = '?string';
                    $composedPrimitiveInputType = 'string';

                } elseif ($composedField instanceof BooleanField || $composedField instanceof BooleansComputedField) {
                    $composedPrimitiveReturnType = '?bool';
                    $composedPrimitiveInputType = 'bool';

                } elseif ($composedField instanceof FloatField) {
                    $composedPrimitiveReturnType = '?float';
                    $composedPrimitiveInputType = 'float';

                } elseif ($composedField instanceof DateTimeField || $composedField instanceof UnixTimeStampField) {
                    $composedPrimitiveReturnType = '?\Carbon\Carbon';
                    $composedPrimitiveInputType = '\Carbon\Carbon|\DateTime|string|int|null';

                } elseif ($composedField instanceof FileField) {
                    $additionalFields = $composedField->isMultiple() ? 'files' : 'file';

                } elseif ($composedField instanceof ForeignKeysField || $composedField instanceof RelatedField || $composedField instanceof RelatedKeysField || $composedField instanceof PivotField) {
                    $relatedSchema = Schema::get($composedField->getComponent());
                    $relatedClassName = $relatedSchema->getInstanceSettings()->getAppClass();

                    if (method_exists($composedField, 'isSingleMode') && $composedField->isSingleMode()) {
                        $composedInstanceReturnType = ':?\\' . $relatedClassName;
                        $composedDocReturn = '@return \\' . $relatedClassName . '|null';
                    } else {
                        $composedInstanceReturnType = ':?\\' . $relatedClassName;
                        $composedDocReturn = '@return \\' . $relatedClassName . '[]';
                    }

                    //@TODO $composedPrimitiveInputType

                } elseif ($composedField instanceof JSONField) {
                    if ($composedField->isAssoc()) {
                        $composedPrimitiveReturnType = '?array';
                        $composedPrimitiveInputType = 'array';
                    } else {
                        $composedPrimitiveReturnType = '?\StdClass';
                        $composedPrimitiveInputType = '\StdClass';
                    }

                } elseif ($composedField instanceof RelatedKeysMergeField) {
                    $composedPrimitiveReturnType = 'array';
                    $composedPrimitiveInputType = 'array';
                }

                if ($composedPrimitiveReturnType !== '') $composedPrimitiveReturnType = ":{$composedPrimitiveReturnType}";

                $templateData = [
                    'fieldName' => $fieldName,
                    'fieldMethod' => $fieldMethod,
                    'composedComponent' => $composedComponent,
                    'composedFieldName' => $composedFieldName,
                    'compositionFieldName' => $compositionFieldName,
                    'composedInstanceReturnType' => $composedInstanceReturnType,
                    'composedDocReturn' => $composedDocReturn,
                    'composedPrimitiveReturnType' => $composedPrimitiveReturnType,
                    'composedPrimitiveInputType' => $composedPrimitiveInputType,
                    'returnSelf' => $returnSelf,
                    'additionalFields' => $additionalFields,
                    'additionalInput' => $additionalInput,
                    'additionalInputDetection' => $additionalInputDetection,
                    'prepareCompositionDataWithField' => $prepareCompositionDataWithField,
                    'nestedCompositionCalls' => implode('', $nestedCompositionCalls),
                ];


                $methods[] = Template::file(__DIR__ . '/../../../assets/phtml/fields/composed-field.phtml')
                    ->setData($templateData)
                    ->parse();
            }
        }

        $traitsUsageString = '';
        if (count($traitsUsage) > 0) {
            $traitsUsageString = '\\' . implode(',\\', $traitsUsage);
        }

        return [
            'methods' => implode("\n", $methods),
            'traits' => $traitsUsageString,
        ];
    }
}