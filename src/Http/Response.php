<?php

namespace Lkt\Http;

use Lkt\Enums\TimeInSeconds;
use Lkt\Http\Traits\ContentTypeTrait;

class Response
{
    use ContentTypeTrait;

    protected int $code = 1;
    protected array|string $responseData = [];

    protected int $headerCacheControlMaxAge = -1;
    protected int $headerExpires = -1;
    protected int $headerLastModified = -1;

    protected string $headerContentDisposition = '';

    protected bool $sendCacheFlag = false;

    public function __construct(int $code = 1, array|string $responseData = [])
    {
        $this->code = $code;
        $this->responseData = $responseData;

        if (is_string($responseData)) {
            $this->setContentTypeTextHTML();
        }
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setResponseData(array $responseData): static
    {
        $this->responseData = $responseData;
        return $this;
    }

    public function getResponseData(): array|string
    {
        return $this->responseData;
    }

    public function setCacheControlMaxAgeHeader(int $time): static
    {
        $this->headerCacheControlMaxAge = $time;
        return $this;
    }

    public function setCacheControlMaxAgeHeaderToOneDay(): static
    {
        $this->headerCacheControlMaxAge = TimeInSeconds::OneDay->value;
        return $this;
    }

    public function setCacheControlMaxAgeHeaderToOneWeek(): static
    {
        $this->headerCacheControlMaxAge = TimeInSeconds::OneWeek->value;
        return $this;
    }

    public function setCacheControlMaxAgeHeaderToOneMonth(): static
    {
        $this->headerCacheControlMaxAge = TimeInSeconds::OneMonth->value;
        return $this;
    }

    public function setCacheControlMaxAgeHeaderToOneYear(): static
    {
        $this->headerCacheControlMaxAge = TimeInSeconds::OneYear->value;
        return $this;
    }

    public function setExpiresHeader(int $time): static
    {
        $this->headerExpires = $time;
        return $this;
    }

    public function setExpiresHeaderToOneDay(): static
    {
        $this->headerExpires = TimeInSeconds::OneDay->value;
        return $this;
    }

    public function setExpiresHeaderToOneWeek(): static
    {
        $this->headerExpires = TimeInSeconds::OneWeek->value;
        return $this;
    }

    public function setExpiresHeaderToOneMonth(): static
    {
        $this->headerExpires = TimeInSeconds::OneMonth->value;
        return $this;
    }

    public function setExpiresHeaderToOneYear(): static
    {
        $this->headerExpires = TimeInSeconds::OneYear->value;
        return $this;
    }

    public function setLastModifiedHeader(int $time): static
    {
        $this->headerLastModified = $time;
        return $this;
    }

    public function setContentDispositionAttachment(string $filename): static
    {
        $this->headerContentDisposition = 'attachment; filename="' . $filename . '"';
        return $this;
    }

    public function enableCache(): static
    {
        $this->sendCacheFlag = true;
        return $this;
    }

    public function enableCacheToOneDay(): static
    {
        return $this->enableCache()
            ->setCacheControlMaxAgeHeaderToOneDay()
            ->setExpiresHeaderToOneDay();
    }

    public function enableCacheToOneWeek(): static
    {
        return $this->enableCache()
            ->setCacheControlMaxAgeHeaderToOneWeek()
            ->setExpiresHeaderToOneWeek();
    }

    public function enableCacheToOneMonth(): static
    {
        return $this->enableCache()
            ->setCacheControlMaxAgeHeaderToOneMonth()
            ->setExpiresHeaderToOneMonth();
    }

    public function enableCacheToOneYear(): static
    {
        return $this->enableCache()
            ->setCacheControlMaxAgeHeaderToOneYear()
            ->setExpiresHeaderToOneYear();
    }

    public function sendHeaders(): static
    {
        $this->sendStatusHeader();
        $this->sendContentTypeHeader();

        if ($this->sendCacheFlag) {
            header('Pragma: cache');
        }

        if ($this->headerCacheControlMaxAge > -1) {
            header("Cache-control: max-age={$this->headerCacheControlMaxAge}");
        }

        if ($this->headerExpires > -1) {
            header('Expires: ' . gmdate(DATE_RFC1123, time() + $this->headerExpires));
        }

        if ($this->headerLastModified > -1) {
            header('Last-Modified: ' . gmdate(DATE_RFC1123, $this->headerLastModified));
        }

        if ($this->headerContentDisposition !== '') {
            header("Content-Disposition: {$this->headerContentDisposition}");
        }

        if ($this->code === -1 || $this->code === 301 || $this->code === 302 || $this->code === 303) {
            header('Location: ' . $this->responseData);
        }

        return $this;
    }

    public function sendStatusHeader(): bool
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'];

        if ($this->code === 200) {
            header("{$protocol} {$this->code} OK");
            return true;
        }

        if ($this->code === 201) {
            header("{$protocol} {$this->code} Created");
            return true;
        }

        if ($this->code === 202) {
            header("{$protocol} {$this->code} Accepted");
            return true;
        }

        if ($this->code === 203) {
            header("{$protocol} {$this->code} Non-Authoritative Information");
            return true;
        }

        if ($this->code === 204) {
            header("{$protocol} {$this->code} No Content");
            return true;
        }

        if ($this->code === 205) {
            header("{$protocol} {$this->code} Reset Content");
            return true;
        }

        if ($this->code === 206) {
            header("{$protocol} {$this->code} Partial Content");
            return true;
        }

        if ($this->code === 300) {
            header("{$protocol} {$this->code} Multiple Choices");
            return true;
        }

        if ($this->code === 301) {
            header("{$protocol} {$this->code} Moved Permanently");
            return true;
        }

        if ($this->code === 302) {
            header("{$protocol} {$this->code} Found");
            return true;
        }

        if ($this->code === 303) {
            header("{$protocol} {$this->code} See Other");
            return true;
        }

        if ($this->code === 304) {
            header("{$protocol} {$this->code} Not Modified");
            return true;
        }

        if ($this->code === 400) {
            header("{$protocol} {$this->code} Bad Request");
            return true;
        }

        if ($this->code === 401) {
            header("{$protocol} {$this->code} Unauthorized");
            return true;
        }

        if ($this->code === 403) {
            header("{$protocol} {$this->code} Forbidden");
            return true;
        }

        if ($this->code === 404) {
            header("{$protocol} {$this->code} Not Found");
            return true;
        }

        if ($this->code === 405) {
            header("{$protocol} {$this->code} Method Not Allowed");
            return true;
        }

        if ($this->code === 406) {
            header("{$protocol} {$this->code} Not Acceptable");
            return true;
        }

        if ($this->code === 407) {
            header("{$protocol} {$this->code} Proxy Authentication Required");
            return true;
        }

        if ($this->code === 408) {
            header("{$protocol} {$this->code} Request Timeout");
            return true;
        }

        if ($this->code === 409) {
            header("{$protocol} {$this->code} Conflict");
            return true;
        }

        if ($this->code === 410) {
            header("{$protocol} {$this->code} Gone");
            return true;
        }

        if ($this->code === 411) {
            header("{$protocol} {$this->code} Length Required");
            return true;
        }

        if ($this->code === 412) {
            header("{$protocol} {$this->code} Precondition Failed");
            return true;
        }

        if ($this->code === 413) {
            header("{$protocol} {$this->code} Content Too Large");
            return true;
        }

        if ($this->code === 414) {
            header("{$protocol} {$this->code} URI Too Long");
            return true;
        }

        if ($this->code === 415) {
            header("{$protocol} {$this->code} Unsupported Media Type");
            return true;
        }

        if ($this->code === 416) {
            header("{$protocol} {$this->code} Range Not Satisfiable");
            return true;
        }

        if ($this->code === 417) {
            header("{$protocol} {$this->code} Expectation Failed");
            return true;
        }

        if ($this->code === 422) {
            header("{$protocol} {$this->code} Unprocessable Content");
            return true;
        }

        if ($this->code === 425) {
            header("{$protocol} {$this->code} Too Early");
            return true;
        }

        if ($this->code === 426) {
            header("{$protocol} {$this->code} Upgrade Required");
            return true;
        }

        if ($this->code === 428) {
            header("{$protocol} {$this->code} Precondition Required");
            return true;
        }

        if ($this->code === 429) {
            header("{$protocol} {$this->code} Too Many Requests");
            return true;
        }

        if ($this->code === 431) {
            header("{$protocol} {$this->code} Request Header Fields Too Large");
            return true;
        }

        if ($this->code === 451) {
            header("{$protocol} {$this->code} Unavailable For Legal Reasons");
            return true;
        }

        if ($this->code === 500) {
            header("{$protocol} {$this->code} Internal Server Error");
            return true;
        }

        if ($this->code === 501) {
            header("{$protocol} {$this->code} Not Implemented");
            return true;
        }

        if ($this->code === 502) {
            header("{$protocol} {$this->code} Bad Gateway");
            return true;
        }

        if ($this->code === 503) {
            header("{$protocol} {$this->code} Service Unavailable");
            return true;
        }
        return false;
    }

    public static function status(int $code = 200, array|string $responseData = []): static
    {
        return new static($code, $responseData);
    }

    public static function redirect(string $responseData = ''): static
    {
        return static::status(-1, $responseData);
    }

    public static function ok(array|string $responseData = []): static
    {
        return static::status(200, $responseData);
    }

    public static function created(array|string $responseData = []): static
    {
        return static::status(201, $responseData);
    }

    public static function accepted(array|string $responseData = []): static
    {
        return static::status(202, $responseData);
    }

    public static function nonAuthoritativeInformation(array|string $responseData = []): static
    {
        return static::status(203, $responseData);
    }

    public static function noContent(array|string $responseData = []): static
    {
        return static::status(204, $responseData);
    }

    public static function resetContent(array|string $responseData = []): static
    {
        return static::status(205, $responseData);
    }

    public static function partialContent(array|string $responseData = []): static
    {
        return static::status(206, $responseData);
    }

    public static function multipleChoices(array|string $responseData = []): static
    {
        return static::status(300, $responseData);
    }

    public static function movedPermanently(array|string $responseData = []): static
    {
        return static::status(301, $responseData);
    }

    public static function found(array|string $responseData = []): static
    {
        return static::status(302, $responseData);
    }

    public static function seeOther(array|string $responseData = []): static
    {
        return static::status(303, $responseData);
    }

    public static function notModified(array|string $responseData = []): static
    {
        return static::status(304, $responseData);
    }

    public static function badRequest(array|string $responseData = []): static
    {
        return static::status(400, $responseData);
    }

    public static function unauthorized(array|string $responseData = []): static
    {
        return static::status(401, $responseData);
    }

    public static function forbidden(array|string $responseData = []): static
    {
        return static::status(403, $responseData);
    }

    public static function notFound(array|string $responseData = []): static
    {
        return static::status(404, $responseData);
    }

    public static function methodNotAllowed(array|string $responseData = []): static
    {
        return static::status(405, $responseData);
    }

    public static function notAcceptable(array|string $responseData = []): static
    {
        return static::status(406, $responseData);
    }

    public static function proxyAuthenticationRequired(array|string $responseData = []): static
    {
        return static::status(407, $responseData);
    }

    public static function requestTimeout(array|string $responseData = []): static
    {
        return static::status(408, $responseData);
    }

    public static function conflict(array|string $responseData = []): static
    {
        return static::status(409, $responseData);
    }

    public static function gone(array|string $responseData = []): static
    {
        return static::status(410, $responseData);
    }

    public static function lengthRequired(array|string $responseData = []): static
    {
        return static::status(411, $responseData);
    }

    public static function preconditionFailed(array|string $responseData = []): static
    {
        return static::status(412, $responseData);
    }

    public static function contentTooLarge(array|string $responseData = []): static
    {
        return static::status(413, $responseData);
    }

    public static function uriTooLong(array|string $responseData = []): static
    {
        return static::status(414, $responseData);
    }

    public static function unsupportedMediaType(array|string $responseData = []): static
    {
        return static::status(415, $responseData);
    }

    public static function rangeNotSatisfiable(array|string $responseData = []): static
    {
        return static::status(416, $responseData);
    }

    public static function expectationFailed(array|string $responseData = []): static
    {
        return static::status(417, $responseData);
    }

    public static function unprocessableContent(array|string $responseData = []): static
    {
        return static::status(422, $responseData);
    }

    public static function tooEarly(array|string $responseData = []): static
    {
        return static::status(425, $responseData);
    }

    public static function upgradeRequired(array|string $responseData = []): static
    {
        return static::status(426, $responseData);
    }

    public static function preconditionRequired(array|string $responseData = []): static
    {
        return static::status(428, $responseData);
    }

    public static function tooManyRequests(array|string $responseData = []): static
    {
        return static::status(429, $responseData);
    }

    public static function requestHeaderFieldsTooLarge(array|string $responseData = []): static
    {
        return static::status(431, $responseData);
    }

    public static function unavailableForLegalReasons(array|string $responseData = []): static
    {
        return static::status(451, $responseData);
    }

    public static function internalServerError(array|string $responseData = []): static
    {
        return static::status(500, $responseData);
    }

    public static function notImplemented(array|string $responseData = []): static
    {
        return static::status(501, $responseData);
    }

    public static function badGateway(array|string $responseData = []): static
    {
        return static::status(502, $responseData);
    }

    public static function serviceUnavailable(array|string $responseData = []): static
    {
        return static::status(503, $responseData);
    }
}