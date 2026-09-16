<?php

namespace Lkt\Templates;

use Lkt\Templates\Traits\BaseTemplate;

class ExtensibleTemplate
{
    use BaseTemplate;

    /**
     * @param array $data
     * @return static
     */
    public static function data(array $data): self
    {
        return (new static())->setData($data);
    }
}