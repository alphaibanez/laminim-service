<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Enums\DateTimeFieldType;
use Lkt\Factory\Fields\Interfaces\NonRelationalField;
use Lkt\Factory\Schemas\Traits\DateFieldWithDefaultValueTrait;
use Lkt\Factory\Schemas\Traits\DateFieldWithFormattedValueTrait;
use Lkt\Factory\Schemas\Traits\FieldWithFormatsOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;

class DateTimeField extends AbstractField implements NonRelationalField
{
    use FieldWithNullOptionTrait,
        FieldWithFormatsOptionTrait,
        DateFieldWithFormattedValueTrait,
        DateFieldWithDefaultValueTrait;

    protected DateTimeFieldType $fieldType = DateTimeFieldType::DateTime;

    public static function unixTimeStamp(string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->fieldType = DateTimeFieldType::UnixTimeStamp;
        return $ins;
    }

    public function isUnixTimeStamp(): bool
    {
        return $this->fieldType === DateTimeFieldType::UnixTimeStamp;
    }
}