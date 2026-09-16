<?php

namespace Lkt\Templates;

use Lkt\Templates\Traits\BaseTemplate;

class Template
{
    use BaseTemplate;

    /**
     * @param string $templatePath
     * @return static
     */
    public static function file(string $templatePath): self
    {
        return new static($templatePath);
    }
}
