<?php

namespace Lkt\Factory\Schemas\Traits;

trait FieldWithAppendForeignKeysNameOptionTrait
{

    protected string $appendForeignKeysName = '';

    public function setAppendForeignKeysName(string $name): static
    {
        $this->appendForeignKeysName = $name;
        return $this;
    }

    public function getAppendForeignKeysName(): string
    {
        return $this->appendForeignKeysName;
    }
}