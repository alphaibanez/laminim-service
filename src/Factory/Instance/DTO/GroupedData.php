<?php

namespace Lkt\Factory\Instance\DTO;

use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Schema;

final readonly class GroupedData
{
    public array $stringData;

    public function __construct(Schema $schema, array $data)
    {
        $stringData = [];

        foreach ($schema->getAllFields() as $field) {
            $k = $field->getName();
            if ($field instanceof StringField) {
                $stringData[$k] = $data[$k];
            }
        }

        $this->stringData = $stringData;
    }
}