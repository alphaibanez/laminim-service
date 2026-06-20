<?php

namespace Lkt\Factory\Instance\DTO;

use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Schema;

final readonly class GroupedData
{
    public array $stringData;
    public array $integerData;

    public function __construct(Schema $schema, array $data)
    {
        $stringData = [];
        $integerData = [];

        foreach ($schema->getAllFields() as $field) {
            $k = $field->getName();

            if ($field instanceof StringField) {
                $stringData[$k] = $data[$k];
            }
            elseif ($field instanceof IntegerField) {
                $integerData[$k] = $data[$k];
            }
        }

        $this->stringData = $stringData;
        $this->integerData = $integerData;
    }
}