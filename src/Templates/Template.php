<?php

namespace Lkt\Templates;

use function Lkt\Tools\System\isAbsolutePath;


class Template extends BaseTemplate
{
    /**
     * @param string $templatePath
     * @return static
     */
    public static function file(string $templatePath): self
    {
        return new static($templatePath);
    }
}
