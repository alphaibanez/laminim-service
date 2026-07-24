<?php

namespace Lkt\CodeMaker\DTO;

use Lkt\Factory\Schemas\Fields\AbstractField;

class FieldGeneratorData
{
    public string $fieldName = '';

    public string $methodName = '';
    public string $selfReturningAnnotation = '';
    public string $relatedComponent = '';
    public string $relatedReturnAnnotation = '';
    public string $relatedReturnType = '';
    public string $relatedQueryBuilder = '';
    public string $additionalInput = '';
    public string $additionalInputDetection = '';

    public array $options = [];
    public array $comparatorsIn = [];

    public bool $isMultiple = false;
    public bool $enabledEmptyPreset = false;

    public string $getterReturnType = '';
    public string $enumChoiceClass = '';
    public AbstractField $field;
}