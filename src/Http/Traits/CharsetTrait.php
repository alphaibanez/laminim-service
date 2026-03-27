<?php

namespace Lkt\Http\Traits;

use Lkt\Http\Enums\Charset;

trait CharsetTrait
{
    protected Charset $charset = Charset::UTF8;

    public function setCharsetUTF8(): static
    {
        $this->charset = Charset::UTF8;
        return $this;
    }

    public function setCharsetNotDefined(): static
    {
        $this->charset = Charset::NotDefined;
        return $this;
    }

    public function setCharset(Charset $charset): static
    {
        $this->charset = $charset;
        return $this;
    }
}