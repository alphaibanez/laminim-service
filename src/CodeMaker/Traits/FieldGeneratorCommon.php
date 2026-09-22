<?php

namespace Lkt\CodeMaker\Traits;

use Lkt\CodeMaker\DTO\FieldGeneratorData;
use Lkt\CodeMaker\FieldGeneration\IntegerChoiceFieldGenerator;
use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Schemas\Schema;

trait FieldGeneratorCommon
{
    public FieldGeneratorData|null $data;
    public Field|null $field = null;
    public Schema|null $schema = null;
    protected string $mode = 'field';


    public function __construct(FieldGeneratorData|null $data =  null)
    {
        $this->data = $data;
    }

    public static function generateCode(FieldGeneratorData $data): string
    {
        return (new static($data))->parse();
    }

    protected function getRelatedReturnTypeFormatted(): string
    {
        if ($this->data->relatedReturnType !== '') return ":?\\{$this->data->relatedReturnType}";
        return '';
    }

    protected function getRelatedReturnAnnotationFormatted(): string
    {
        if ($this->data->relatedReturnAnnotation !== '') {
            if ($this->data->isMultiple) {
                return "@return \\{$this->data->relatedReturnAnnotation}[]";
            }
            return "@return \\{$this->data->relatedReturnAnnotation}";
        }
        return '';
    }

    public function getAllowedOptionsMethods(): array
    {
        $r = [];
        if ($this instanceof IntegerChoiceFieldGenerator) {
            foreach ($this->data->options as $key => $value) {
                $d = is_numeric($key) ? trim($value) : trim($key);
                $d = str_replace(' ', '', ucwords(str_replace('-', ' ', $d)));
                $r[$key] = $d;
            }
        } else {
            $r = array_map(function ($option) {
                return str_replace(' ', '', ucwords(str_replace('-', ' ', $option)));
            }, $this->data->options);
        }
        return $r;
    }

    public function getEnumChoiceClass(): string
    {
        $r = $this->data->enumChoiceClass;
        if ($r !== '') $r = "|\\{$r}";
        return $r;
    }

    public static function queryBuilder(Field $field, Schema $schema): static
    {
        $ins = new static();
        $ins->field = $field;
        $ins->schema = $schema;
        $ins->mode = 'query';
        return $ins;
    }
}