<?php

namespace Lkt\Templates;

class ExtensibleTemplate extends BaseTemplate
{
    /**
     * @param array $data
     * @return static
     */
    public static function data(array $data): self
    {
        return (new static())->setData($data);
    }
}