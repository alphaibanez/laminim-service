<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Schema;
use Lkt\Locale\Locale;

final class JsonDataController
{
    private array $data = [];
    private array $payload = [];

    private Schema $schema;
    private Item $item;

    public function __construct(Schema &$schema, Item &$ins, array $data)
    {
        $this->schema = $schema;
        $this->item = $ins;
        foreach ($data as $k => $datum) $this->setOriginal($k, $datum);
    }

    public function get(string $key): array|\StdClass|null
    {
        if (array_key_exists($key, $this->payload)) {
            return $this->payload[$key];
        }

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    public function has(string $key): bool
    {
        $v = $this->get($key);

        $f = $this->schema->getJSONField($key);
        $mode = $f->getEmptyDataMode();

        if ($mode === EmptyDataMode::OnlyNull) return $v !== null;
        if ($f->isAssoc()) return count($v) > 0;
        return count((array)$v);
    }

    public function set(string $key, $value): self
    {
        $f = $this->schema->getJSONField($key);
        if (!$f) {
            throw InvalidItemDataAssignException::missingField($key);
        }

        $currentValue = $this->get($key);
        $parsedValue = $this->parse($key, $value);

        if ($parsedValue !== $currentValue) {
            $this->payload[$key] = $parsedValue;
        }

        return $this;
    }

    public function parse(string $key, $value): array|\StdClass|null
    {
        if ($value === null) return null;

        $f = $this->schema->getJSONField($key);
        $associative = $f->isAssoc();

        $r = null;

        if (is_array($value)) {
            if ($associative) {
                $r = $value;
            } else {
                $r = json_decode(json_encode($value), false);
            }
        }

        elseif (is_object($value)) {
            if ($associative) {
                $r = json_decode(json_encode($value), true);
            } else {
                $r = $value;
            }
        }

        elseif (is_string($value)){
            $value = htmlspecialchars_decode($value, JSON_UNESCAPED_UNICODE|ENT_QUOTES);

            // Decodes legacy escape patters previous to tables encoded as utf8mb4_unicode_ci
            // @todo someday should be removed
            $value = str_replace(':LKT_SLASH:', '\\', $value);
            $value = str_replace(':LKT_QUESTION_MARK:', '?', $value);
            $value = str_replace(':LKT_SINGLE_QUOTE:', "'", $value);
            $value = trim(str_replace('\"', '"', $value));

            $r = json_decode($value, $associative);
        }

        if ($r !== null) {
            if ($associative) {
                $availableLanguages = Locale::getAvailableLangCodesValues();
                foreach ($availableLanguages as $language) {
                    if (!isset($r[$language])) $r[$language] = '';
                    $r[$language] = htmlspecialchars_decode($r[$language], JSON_UNESCAPED_UNICODE|ENT_QUOTES);
                }
            }
            return $r;
        }

        return json_decode('{}', $associative);
    }

    public function getOriginal(string $key): array|\StdClass|null
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    public function setOriginal(string $key, $value): self
    {
        $parsedValue = $this->parse($key, $value);
        $this->data[$key] = $parsedValue;
        return $this;
    }

    public function dumpPayloadIntoOriginal(): self
    {
        $this->data = [...$this->data, ... $this->payload];
        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getOriginalData(): array
    {
        return $this->data;
    }

    public function __debugInfo() {
        return [
            'data' => $this->data,
            'payload' => $this->payload,
        ];
    }
}