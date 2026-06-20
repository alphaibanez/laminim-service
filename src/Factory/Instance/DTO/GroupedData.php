<?php

namespace Lkt\Factory\Instance\DTO;

use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Schema;

final readonly class GroupedData
{
    public array $stringData;
    public array $integerData;
    public array $multipleIntegerData;

    public function __construct(Schema $schema, array $data)
    {
        $stringData = [];
        $integerData = [];
        $multipleIntegerData = [];

        foreach ($schema->getAllFields() as $field) {
            $k = $field->getName();

            if ($field instanceof StringField) {
                $stringData[$k] = $data[$k];
            }
            elseif ($field instanceof IntegerField) {
                if ($field->isMultiple()) {
                    $multipleIntegerData[$k] = $data[$k];
                } else {
                    $integerData[$k] = $data[$k];
                }
            }
        }

        $this->stringData = $stringData;
        $this->integerData = $integerData;
        $this->multipleIntegerData = $multipleIntegerData;
    }
}