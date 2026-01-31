<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Schemas\Exceptions\InvalidFieldFilePathException;
use Lkt\Factory\Schemas\Traits\FieldWithMultipleOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;
use Lkt\Factory\Schemas\Values\FieldFilePathValue;
use Lkt\MIME;

class FileField extends AbstractField
{
    const TYPE = 'file';

    use FieldWithNullOptionTrait,
        FieldWithMultipleOptionTrait;

    protected ?FieldFilePathValue $storePath = null;
    protected ?FieldFilePathValue $publicPath = null;

    protected string|int|float|null $maxFileSize = null;

    protected float|null $httpCacheDurationInSeconds = null;

    /**
     * @var MIME[]
     */
    protected array $supportedFormats = [];
    protected string|null $fileName = null;


    /**
     * @throws InvalidFieldFilePathException
     */
    final public function setStorePath(string|callable $path): self
    {
        $this->storePath = new FieldFilePathValue($path);
        return $this;
    }

    final public function getStorePath($instance = null): string
    {
        if ($this->storePath instanceof FieldFilePathValue) {
            return $this->storePath->getValue($instance);
        }
        return '';
    }


    /**
     * @throws InvalidFieldFilePathException
     */
    final public function setPublicPath(string|callable $path): self
    {
        $this->publicPath = new FieldFilePathValue($path);
        return $this;
    }

    final public function getPublicPath($instance = null): string
    {
        if ($this->publicPath instanceof FieldFilePathValue) {
            return $this->publicPath->getValue($instance);
        }
        return '';
    }

    /**
     * @return bool
     */
    final public function hasPublicPath($instance = null): bool
    {
        return $this->getPublicPath($instance) !== '';
    }

    final public function hasHttpCacheDurationInSeconds(): bool
    {
        return $this->httpCacheDurationInSeconds !== null;
    }

    final public function getHttpCacheDurationInSeconds(): float
    {
        return (float)$this->httpCacheDurationInSeconds;
    }

    final public function setHttpCacheDurationInSeconds(float $seconds): static
    {
        $this->httpCacheDurationInSeconds = $seconds;
        return $this;
    }

    final public function setHttpCacheDurationInSecondsToOneDay(): static
    {
        $this->httpCacheDurationInSeconds = 86400;
        return $this;
    }

    final public function setHttpCacheDurationInSecondsToOneWeek(): static
    {
        $this->httpCacheDurationInSeconds = 604800;
        return $this;
    }

    final public function setHttpCacheDurationInSecondsToOneMonth(): static
    {
        $this->httpCacheDurationInSeconds = 2419200;
        return $this;
    }

    final public function setHttpCacheDurationInSecondsToOneYear(): static
    {
        $this->httpCacheDurationInSeconds = 31536000;
        return $this;
    }

    final public function setMaxFileSize(string|int|float|null $maxFileSize): static
    {
        $this->maxFileSize = $maxFileSize;
        return $this;
    }

    final public function getMaxFileSize(): string|int|float
    {
        if (is_null($this->maxFileSize)) return '10M';
        return $this->maxFileSize;
    }

    /**
     * @param MIME[] $formats
     * @return $this
     */
    final public function setSupportedFormats(array $formats): static
    {
        $this->supportedFormats = $formats;
        return $this;
    }

    /**
     * @return MIME[]
     */
    final public function getSupportedFormats(): array
    {
        return $this->supportedFormats;
    }

    final public function setFileName(string $name): static
    {
        $this->fileName = $name;
        return $this;
    }

    final public function getFileName(): string|null
    {
        return $this->fileName;
    }
}