<?php

namespace Lkt\FileUpload\FileNameGenerator;

use Closure;
use Lkt\FileUpload\FileUpload;

class Custom implements FileNameGenerator
{
    protected $generator;

    public function __construct(callable|Closure|string $nameGenerator)
    {
        $this->generator = $nameGenerator;
    }

    public function getFileName(string $source_name, string $type, string $tmp_name, int $index, array $content_range, FileUpload $upload): string
    {
        if (is_string($this->generator) && ! is_callable($this->generator)) {
            return $this->generator;
        }

        return call_user_func_array(
            $this->generator,
            func_get_args()
        );
    }
}
