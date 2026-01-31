<?php

namespace Lkt\Http\Traits;

use Lkt\Http\Enums\Charset;

trait CharsetTrait
{
    protected string $charset = Charset::UTF8;

    public function setCharsetUTF8(): static
    {
        $this->charset = Charset::UTF8;
        return $this;
    }

    public function setCharset(string $charset): static
    {
        $this->charset = $charset;
        return $this;
    }
}