<?php

namespace Lkt\Http\Traits;

use Lkt\MIME;

trait ContentTypeTrait
{
    use CharsetTrait;

    protected string $contentType = MIME::JSON;
    protected int|null $jsonEncodingFlag = null;

    public function setJSONEncodingFlag(mixed $jsonEncodingFlag): static
    {
        $this->jsonEncodingFlag = $jsonEncodingFlag;
        return $this;
    }

    public function setContentTypeMIME(string $mime): static
    {
        $this->contentType = $mime;
        return $this;
    }

    public function setContentTypeJSON(): static
    {
        $this->contentType = MIME::JSON;
        return $this;
    }

    public function setContentTypeTextHTML(): static
    {
        $this->contentType = MIME::HTML;
        return $this;
    }

    public function setContentTypeTextXML(): static
    {
        $this->contentType = MIME::XML;
        return $this;
    }

    public function setContentTypeByFileExtension(string $extension): static
    {
        $this->contentType = MIME::getByExtension($extension);
        return $this;
    }

    public function isJSONContentType(): bool
    {
        return $this->contentType === MIME::JSON;
    }

    public function isTextHTMLContentType(): bool
    {
        return $this->contentType === MIME::HTML;
    }

    public function isTextXMLContentType(): bool
    {
        return $this->contentType === MIME::XML;
    }

    public function isImageContentType(): bool
    {
        return in_array($this->contentType, [
            MIME::GIF,
            MIME::PNG,
            MIME::JPEG,
            MIME::SVG,
            MIME::WEBP,
            MIME::AVIF,
            MIME::BMP,
            MIME::TIFF,
        ]);
    }

    public function sendContentTypeHeader(): bool
    {
        $contentType = $this->contentType;
        $charset = $this->charset;

        $aux = [$contentType];
        if ($charset !== '') {
            $aux[] = "charset={$charset}";
        }
        $header = implode(';', $aux);

        header("Content-Type: {$header}");

        return true;
    }

    public function sendContent(): static
    {
        if ($this->isJSONContentType()) {
            $data = $this->getResponseData();
            if (is_array($data) && count($data) > 0){
                echo json_encode($data, $this->jsonEncodingFlag);
            }
        } else {
            $data = $this->getResponseData();
            if (is_string($data)){
                echo $data;
            }
        }

        return $this;
    }
}