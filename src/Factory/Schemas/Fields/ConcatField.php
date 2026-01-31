<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Schema;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class ConcatField extends AbstractField
{
    use FieldWithNullOptionTrait;

    protected array $fields = [];
    protected string $separator = '';


    final public static function concat(string $name, array $fields, string $separator): static
    {
        $r = new static($name, '');
        $r->fields = $fields;
        $r->separator = $separator;
        return $r;
    }

    public function getConcatenatedFields(): array
    {
        return $this->fields;
    }

    public function getConcatenatedFieldsAsString(Schema $schema): string
    {
        $r = [];
        foreach ($this->fields as $field) {
            $f = $schema->getField($field);
            $r[] = "'{$f->getColumn()}'";
        }
        $r = implode(',', $r);
        return "[{$r}]";
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }
}