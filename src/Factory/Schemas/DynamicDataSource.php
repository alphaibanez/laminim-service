<?php

namespace Lkt\Factory\Schemas;

use Lkt\Debug\VarDumper;

class DynamicDataSource
{
    protected function __construct(
        readonly mixed $callable = null,
    )
    {

    }

    public static function calculated(callable $getter): static
    {
        return new static(callable: $getter);
    }

    public function getSchema(Schema $schema, array|null $args = null): Schema
    {
        if (is_callable($this->callable)) {
            /** @var DynamicDataSourcePayload $payload */
            $payload = call_user_func_array($this->callable, $args);
        }
        return $schema->getDynamicDataSource($payload);
    }
}