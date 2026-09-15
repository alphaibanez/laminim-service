<?php

namespace Lkt\Http;

use Lkt\Enums\TimeInSeconds;
use Lkt\Http\Enums\HttpStatus;
use Lkt\Http\Traits\ContentTypeTrait;
use PhpOffice\PhpSpreadsheet\Writer\BaseWriter;

class Response
{
    use ContentTypeTrait;

    protected HttpStatus $code = HttpStatus::NotDefined;
    protected array|string|BaseWriter $responseData = [];

    protected int $headerCacheControlMaxAge = -1;
    protected int $headerExpires = -1;
    protected int $headerLastModified = -1;

    protected string $headerContentDisposition = '';

    protected bool $sendCacheFlag = false;

    protected array $customHeaders = [];

    protected function __construct(HttpStatus $code = HttpStatus::NotDefined, array|string|BaseWriter $responseData = [])
    {
        $this->code = $code;
        $this->responseData = $responseData;

        if (is_string($responseData)) {
            $this->setContentTypeTextHTML();
        }
    }

    public function getCode(): HttpStatus
    {
        return $this->code;
    }

    public function setResponseData(array $responseData): static
    {
        $this->responseData = $responseData;
        return $this;
    }

    public function getResponseData(): array|string|BaseWriter
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

    public function setCustomHeaders(array $headers): static
    {
        $this->customHeaders = $headers;
        return $this;
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

        if ($this->code === HttpStatus::UnknownRedirect || $this->code === HttpStatus::MovedPermanently || $this->code === HttpStatus::Found || $this->code === HttpStatus::SeeOther) {
            header('Location: ' . $this->responseData);
        }

        foreach ($this->customHeaders as $header => $value) {
            header("{$header}: {$value}");
        }

        return $this;
    }

    public function sendStatusHeader(): bool
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'];

        if ($this->code === HttpStatus::Ok) {
            header("{$protocol} {$this->code->value} OK");
            return true;
        }

        if ($this->code === HttpStatus::Created) {
            header("{$protocol} {$this->code->value} Created");
            return true;
        }

        if ($this->code === HttpStatus::Accepted) {
            header("{$protocol} {$this->code->value} Accepted");
            return true;
        }

        if ($this->code === HttpStatus::NonAuthoritativeInformation) {
            header("{$protocol} {$this->code->value} Non-Authoritative Information");
            return true;
        }

        if ($this->code === HttpStatus::NoContent) {
            header("{$protocol} {$this->code->value} No Content");
            return true;
        }

        if ($this->code === HttpStatus::ResetContent) {
            header("{$protocol} {$this->code->value} Reset Content");
            return true;
        }

        if ($this->code === HttpStatus::PartialContent) {
            header("{$protocol} {$this->code->value} Partial Content");
            return true;
        }

        if ($this->code === HttpStatus::MultipleChoices) {
            header("{$protocol} {$this->code->value} Multiple Choices");
            return true;
        }

        if ($this->code === HttpStatus::MovedPermanently) {
            header("{$protocol} {$this->code->value} Moved Permanently");
            return true;
        }

        if ($this->code === HttpStatus::Found) {
            header("{$protocol} {$this->code->value} Found");
            return true;
        }

        if ($this->code === HttpStatus::SeeOther) {
            header("{$protocol} {$this->code->value} See Other");
            return true;
        }

        if ($this->code === HttpStatus::NotModified) {
            header("{$protocol} {$this->code->value} Not Modified");
            return true;
        }

        if ($this->code === HttpStatus::BadRequest) {
            header("{$protocol} {$this->code->value} Bad Request");
            return true;
        }

        if ($this->code === HttpStatus::Unauthorized) {
            header("{$protocol} {$this->code->value} Unauthorized");
            return true;
        }

        if ($this->code === HttpStatus::Forbidden) {
            header("{$protocol} {$this->code->value} Forbidden");
            return true;
        }

        if ($this->code === HttpStatus::NotFound) {
            header("{$protocol} {$this->code->value} Not Found");
            return true;
        }

        if ($this->code === HttpStatus::MethodNotAllowed) {
            header("{$protocol} {$this->code->value} Method Not Allowed");
            return true;
        }

        if ($this->code === HttpStatus::NotAcceptable) {
            header("{$protocol} {$this->code->value} Not Acceptable");
            return true;
        }

        if ($this->code === HttpStatus::ProxyAuthenticationRequired) {
            header("{$protocol} {$this->code->value} Proxy Authentication Required");
            return true;
        }

        if ($this->code === HttpStatus::RequestTimeout) {
            header("{$protocol} {$this->code->value} Request Timeout");
            return true;
        }

        if ($this->code === HttpStatus::Conflict) {
            header("{$protocol} {$this->code->value} Conflict");
            return true;
        }

        if ($this->code === HttpStatus::Gone) {
            header("{$protocol} {$this->code->value} Gone");
            return true;
        }

        if ($this->code === HttpStatus::LengthRequired) {
            header("{$protocol} {$this->code->value} Length Required");
            return true;
        }

        if ($this->code === HttpStatus::PreconditionFailed) {
            header("{$protocol} {$this->code->value} Precondition Failed");
            return true;
        }

        if ($this->code === HttpStatus::ContentTooLarge) {
            header("{$protocol} {$this->code->value} Content Too Large");
            return true;
        }

        if ($this->code === HttpStatus::UriTooLong) {
            header("{$protocol} {$this->code->value} URI Too Long");
            return true;
        }

        if ($this->code === HttpStatus::UnsupportedMediaType) {
            header("{$protocol} {$this->code->value} Unsupported Media Type");
            return true;
        }

        if ($this->code === HttpStatus::RangeNotSatisfiable) {
            header("{$protocol} {$this->code->value} Range Not Satisfiable");
            return true;
        }

        if ($this->code === HttpStatus::ExpectationFailed) {
            header("{$protocol} {$this->code->value} Expectation Failed");
            return true;
        }

        if ($this->code === HttpStatus::UnprocessableContent) {
            header("{$protocol} {$this->code->value} Unprocessable Content");
            return true;
        }

        if ($this->code === HttpStatus::TooEarly) {
            header("{$protocol} {$this->code->value} Too Early");
            return true;
        }

        if ($this->code === HttpStatus::UpgradeRequired) {
            header("{$protocol} {$this->code->value} Upgrade Required");
            return true;
        }

        if ($this->code === HttpStatus::PreconditionRequired) {
            header("{$protocol} {$this->code->value} Precondition Required");
            return true;
        }

        if ($this->code === HttpStatus::TooManyRequests) {
            header("{$protocol} {$this->code->value} Too Many Requests");
            return true;
        }

        if ($this->code === HttpStatus::RequestHeaderFieldsTooLarge) {
            header("{$protocol} {$this->code->value} Request Header Fields Too Large");
            return true;
        }

        if ($this->code === HttpStatus::UnavailableForLegalReasons) {
            header("{$protocol} {$this->code->value} Unavailable For Legal Reasons");
            return true;
        }

        if ($this->code === HttpStatus::InternalServerError) {
            header("{$protocol} {$this->code->value} Internal Server Error");
            return true;
        }

        if ($this->code === HttpStatus::NotImplemented) {
            header("{$protocol} {$this->code->value} Not Implemented");
            return true;
        }

        if ($this->code === HttpStatus::BadGateway) {
            header("{$protocol} {$this->code->value} Bad Gateway");
            return true;
        }

        if ($this->code === HttpStatus::ServiceUnavailable) {
            header("{$protocol} {$this->code->value} Service Unavailable");
            return true;
        }
        return false;
    }

    public static function status(HttpStatus $code = HttpStatus::Ok, array|string|BaseWriter $responseData = []): static
    {
        return new static($code, $responseData);
    }

    public static function redirect(string $responseData = ''): static
    {
        return static::status(HttpStatus::UnknownRedirect, $responseData);
    }

    public static function ok(array|string|BaseWriter $responseData = []): static
    {
        return static::status(HttpStatus::Ok, $responseData);
    }

    public static function created(array|string $responseData = []): static
    {
        return static::status(HttpStatus::Created, $responseData);
    }

    public static function accepted(array|string $responseData = []): static
    {
        return static::status(HttpStatus::Accepted, $responseData);
    }

    public static function nonAuthoritativeInformation(array|string $responseData = []): static
    {
        return static::status(HttpStatus::NonAuthoritativeInformation, $responseData);
    }

    public static function noContent(array|string $responseData = []): static
    {
        return static::status(HttpStatus::NoContent, $responseData);
    }

    public static function resetContent(array|string $responseData = []): static
    {
        return static::status(HttpStatus::ResetContent, $responseData);
    }

    public static function partialContent(array|string $responseData = []): static
    {
        return static::status(HttpStatus::PartialContent, $responseData);
    }

    public static function multipleChoices(array|string $responseData = []): static
    {
        return static::status(HttpStatus::MultipleChoices, $responseData);
    }

    public static function movedPermanently(array|string $responseData = []): static
    {
        return static::status(HttpStatus::MovedPermanently, $responseData);
    }

    public static function found(array|string $responseData = []): static
    {
        return static::status(HttpStatus::Found, $responseData);
    }

    public static function seeOther(array|string $responseData = []): static
    {
        return static::status(HttpStatus::SeeOther, $responseData);
    }

    public static function notModified(array|string $responseData = []): static
    {
        return static::status(HttpStatus::NotModified, $responseData);
    }

    public static function badRequest(array|string $responseData = []): static
    {
        return static::status(HttpStatus::BadRequest, $responseData);
    }

    public static function unauthorized(array|string $responseData = []): static
    {
        return static::status(HttpStatus::Unauthorized, $responseData);
    }

    public static function forbidden(array|string $responseData = []): static
    {
        return static::status(HttpStatus::Forbidden, $responseData);
    }

    public static function notFound(array|string $responseData = []): static
    {
        return static::status(HttpStatus::NotFound, $responseData);
    }

    public static function methodNotAllowed(array|string $responseData = []): static
    {
        return static::status(HttpStatus::MethodNotAllowed, $responseData);
    }

    public static function notAcceptable(array|string $responseData = []): static
    {
        return static::status(HttpStatus::NotAcceptable, $responseData);
    }

    public static function proxyAuthenticationRequired(array|string $responseData = []): static
    {
        return static::status(HttpStatus::ProxyAuthenticationRequired, $responseData);
    }

    public static function requestTimeout(array|string $responseData = []): static
    {
        return static::status(HttpStatus::RequestTimeout, $responseData);
    }

    public static function conflict(array|string $responseData = []): static
    {
        return static::status(HttpStatus::Conflict, $responseData);
    }

    public static function gone(array|string $responseData = []): static
    {
        return static::status(HttpStatus::Gone, $responseData);
    }

    public static function lengthRequired(array|string $responseData = []): static
    {
        return static::status(HttpStatus::LengthRequired, $responseData);
    }

    public static function preconditionFailed(array|string $responseData = []): static
    {
        return static::status(HttpStatus::PreconditionFailed, $responseData);
    }

    public static function contentTooLarge(array|string $responseData = []): static
    {
        return static::status(HttpStatus::ContentTooLarge, $responseData);
    }

    public static function uriTooLong(array|string $responseData = []): static
    {
        return static::status(HttpStatus::UriTooLong, $responseData);
    }

    public static function unsupportedMediaType(array|string $responseData = []): static
    {
        return static::status(HttpStatus::UnsupportedMediaType, $responseData);
    }

    public static function rangeNotSatisfiable(array|string $responseData = []): static
    {
        return static::status(HttpStatus::RangeNotSatisfiable, $responseData);
    }

    public static function expectationFailed(array|string $responseData = []): static
    {
        return static::status(HttpStatus::ExpectationFailed, $responseData);
    }

    public static function unprocessableContent(array|string $responseData = []): static
    {
        return static::status(HttpStatus::UnprocessableContent, $responseData);
    }

    public static function tooEarly(array|string $responseData = []): static
    {
        return static::status(HttpStatus::TooEarly, $responseData);
    }

    public static function upgradeRequired(array|string $responseData = []): static
    {
        return static::status(HttpStatus::UpgradeRequired, $responseData);
    }

    public static function preconditionRequired(array|string $responseData = []): static
    {
        return static::status(HttpStatus::PreconditionRequired, $responseData);
    }

    public static function tooManyRequests(array|string $responseData = []): static
    {
        return static::status(HttpStatus::TooManyRequests, $responseData);
    }

    public static function requestHeaderFieldsTooLarge(array|string $responseData = []): static
    {
        return static::status(HttpStatus::RequestHeaderFieldsTooLarge, $responseData);
    }

    public static function unavailableForLegalReasons(array|string $responseData = []): static
    {
        return static::status(HttpStatus::UnavailableForLegalReasons, $responseData);
    }

    public static function internalServerError(array|string $responseData = []): static
    {
        return static::status(HttpStatus::InternalServerError, $responseData);
    }

    public static function notImplemented(array|string $responseData = []): static
    {
        return static::status(HttpStatus::NotImplemented, $responseData);
    }

    public static function badGateway(array|string $responseData = []): static
    {
        return static::status(HttpStatus::BadGateway, $responseData);
    }

    public static function serviceUnavailable(array|string $responseData = []): static
    {
        return static::status(HttpStatus::ServiceUnavailable, $responseData);
    }
}