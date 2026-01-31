<?php

namespace Lkt\Factory\Schemas\Traits;

trait FieldWithFormatsOptionTrait
{
    protected array $formats = [];

    public function addFormat(string $name, string $format): static
    {
        $this->formats[$name] = $format;
        return $this;
    }

    public function addEnglishInternationalStandardFormat(bool $useIntl = true): static
    {
        if ($useIntl) {
            $this->formats['englishInternationalStandard'] = [$useIntl, 'y-MM-dd'];

        } else {
            $this->formats['englishInternationalStandard'] = [$useIntl, 'Y-m-d'];

        }
        return $this;
    }

    public static function englishInternationalStandard(string $name, string $column = ''): static
    {
        $r = new static($name, $column);
        $r->addEnglishInternationalStandardFormat();
        return $r;
    }

    public function addAmericanEnglishFormat(bool $useIntl = true): static
    {
        if ($useIntl) {
            $this->formats['americanEnglish'] = [$useIntl, 'MMMM d, y'];

        } else {
            $this->formats['americanEnglish'] = [$useIntl, 'F d, Y'];

        }
        return $this;
    }

    public static function americanEnglish(string $name, string $column = ''): static
    {
        $r = new static($name, $column);
        $r->addAmericanEnglishFormat();
        return $r;
    }

    public function addBritishEnglishFormat(bool $useIntl = true): static
    {
        if ($useIntl) {
            $this->formats['britishEnglish'] = [$useIntl, 'd MMMM y'];

        } else {
            $this->formats['britishEnglish'] = [$useIntl, 'd F Y'];

        }
        return $this;
    }

    public static function britishEnglish(string $name, string $column = ''): static
    {
        $r = new static($name, $column);
        $r->addBritishEnglishFormat();
        return $r;
    }

    public function addATOMFormat(): static
    {
        $this->formats['ATOM'] = [false, DATE_ATOM];
        return $this;
    }

    public function addCookieFormat(): static
    {
        $this->formats['cookie'] = [false, DATE_COOKIE];
        return $this;
    }

    public function addRFC822Format(): static
    {
        $this->formats['RFC822'] = [false, DATE_RFC822];
        return $this;
    }

    public function addRFC850Format(): static
    {
        $this->formats['RFC850'] = [false, DATE_RFC850];
        return $this;
    }

    public function addRFC1036Format(): static
    {
        $this->formats['RFC1036'] = [false, DATE_RFC1036];
        return $this;
    }

    public function addRFC1123Format(): static
    {
        $this->formats['RFC1123'] = [false, DATE_RFC1123];
        return $this;
    }

    public function addRFC2822Format(): static
    {
        $this->formats['RFC2822'] = [false, DATE_RFC2822];
        return $this;
    }

    public function addRSSFormat(): static
    {
        $this->formats['RSS'] = [false, DATE_RSS];
        return $this;
    }

    public function addW3CFormat(): static
    {
        $this->formats['W3C'] = [false, DATE_W3C];
        return $this;
    }

    public function hasFormats(): bool
    {
        return count($this->formats) > 0;
    }

    public function getFormats(): array
    {
        return $this->formats;
    }
}