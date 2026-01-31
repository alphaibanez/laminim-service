<?php

namespace Lkt\FileUpload\PathResolver;

class Simple implements PathResolver
{
    /**
     * Main path
     */
    protected string $mainPath;

    /**
     * A construct to remember
     */
    public function __construct(string $main_path)
    {
        $this->mainPath = $main_path;
    }

    /**
     * @see PathResolver
     */
    public function getUploadPath(?string $name = null): string
    {
        return $this->mainPath . '/' . $name;
    }

    /**
     * @see PathResolver
     */
    public function upcountName(?string $name): string
    {
        return preg_replace_callback('/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/', function ($matches) {
            $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
            $ext = isset($matches[2]) ? $matches[2] : '';

            return ' (' . $index . ')' . $ext;
        }, $name, 1);
    }
}
