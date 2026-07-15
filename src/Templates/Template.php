<?php

namespace Lkt\Templates;


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
