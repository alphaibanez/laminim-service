<?php

namespace Lkt\Factory\Fields\Traits;

use Lkt\Factory\Instantiator\Exceptions\MinLengthRequiredException;

trait FieldWithLengthLimits
{
    protected int|null $minLength = null;
    protected int|null $maxLength = null;

    public function limitLength(int $min = null, int $max = null): static
    {
        $this->minLength = $min;
        $this->maxLength = $max;
        return $this;
    }

    public function hasMinLength(): bool
    {
        return $this->minLength !== null;
    }

    public function hasMaxLength(): bool
    {
        return $this->maxLength !== null;
    }

    public function getMinLength(): int
    {
        return $this->minLength;
    }

    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    public function checkMinLength(string $value): bool
    {
        return strlen($value) >= $this->minLength;
    }

    public function checkMaxLength(string $value): bool
    {
        return strlen($value) <= $this->maxLength;
    }
}