<?php

namespace Lkt\Factory\Instance\DataControllers;

use Lkt\Factory\Instance\Enums\EmptyDataMode;
use Lkt\Factory\Instance\Enums\InvalidDataMode;
use Lkt\Factory\Instance\Enums\TrimMode;
use Lkt\Factory\Instance\Interfaces\Item;
use Lkt\Factory\Instantiator\Exceptions\InvalidIntegerChoiceValueException;
use Lkt\Factory\Instantiator\Exceptions\MaxLengthRequiredException;
use Lkt\Factory\Instantiator\Exceptions\MinLengthRequiredException;
use Lkt\Factory\Schemas\Exceptions\DuplicatedValueException;
use Lkt\Factory\Schemas\Exceptions\InvalidItemDataAssignException;
use Lkt\Factory\Schemas\Fields\StringChoiceField;
use Lkt\Factory\Schemas\Schema;

final class StringDataController
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

    public function get(string $key): string|null
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

        $f = $this->schema->getStringField($key);
        $mode = $f->getEmptyDataMode();

        if ($mode === EmptyDataMode::OnlyNull) return $v !== null;
        return $v !== '';
    }

    public function set(string $key, $value): self
    {
        $f = $this->schema->getStringField($key);
        if (!$f) {
            throw InvalidItemDataAssignException::missingField($key);
        }

        if (is_object($f) && method_exists($f, 'isUnique') && $f->isUnique()) {
            $builder = $this->schema->getQueryBuilder();
            $this->schema->filterBuilderWithUniqueData($builder, [$key => $value]);
            $result = $this->schema->getOne($builder);
            if ($result instanceof Item && !$result->isSameIdentifierValue($this->item->getIdentifierValue())) {
                throw DuplicatedValueException::getInstance($value);
            }
        }

        $currentValue = $this->get($key);
        $parsedValue = $this->parse($key, $value);

        if ($parsedValue !== $currentValue) {
            $this->payload[$key] = $parsedValue;
        }

        return $this;
    }

    public function parse(string $key, $value): string|null
    {
        if ($value === null) return null;

        $field = $this->schema->getStringField($key);
        $trimMode = method_exists($field, 'getTrimMode') ? $field->getTrimMode() : TrimMode::None;

        if (is_object($value) && property_exists($value, 'value') && isset($value->value)) {
            $value = $value->value;
        }

        if (is_string($value)) {
            $value = $this->trim($value, $trimMode);

            if ($field->hasMinLength() && !$field->checkMinLength($value)) {
                throw MinLengthRequiredException::getInstance($value, $field->getMinLength());
            }

            if ($field->hasMaxLength() && !$field->checkMaxLength($value)) {
                throw MaxLengthRequiredException::getInstance($value, $field->getMaxLength());
            }
        }

        if (method_exists($field, 'isI18nJson') && $field->isI18nJson()) {
            if (is_array($value)) { // If multi lang data given
                foreach ($value as &$v) $v = htmlspecialchars_decode($v, JSON_UNESCAPED_UNICODE|ENT_QUOTES);
            } else {
                $value = htmlspecialchars_decode($value, JSON_UNESCAPED_UNICODE|ENT_QUOTES);
            }
//            $value = str_replace(':LKT_SLASH:', '\\', $value);
//            $value = str_replace(':LKT_QUESTION_MARK:', '?', $value);
//            $value = str_replace(':LKT_SINGLE_QUOTE:', "'", $value);
//            $value = trim(str_replace('\"', '"', $value));

        } else if ($field->isHTML()) {
            // Decodes legacy escape patters previous to tables encoded as utf8mb4_unicode_ci
            // @todo someday should be removed
            $value = str_replace(':LKT_SLASH:', '\\', $value);
            $value = str_replace(':LKT_QUESTION_MARK:', '?', $value);
            $value = str_replace(':LKT_SINGLE_QUOTE:', "'", $value);
            $value = trim(str_replace('\"', '"', $value));
        }

        if ($field instanceof StringChoiceField) {
            $availableOptions = $field->getAllowedOptions();

            if (!in_array($value, $availableOptions, true)) {
                throw InvalidIntegerChoiceValueException::getInstance($value, $key, $this->schema->getComponent());
            }

            return $value;

        } elseif ($field->isEmail()) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return $value;
            }
        }

        $mode = $field->getInvalidDataMode();

        return match ($mode) {
            InvalidDataMode::CastToType => $this->trim((string)$value, $trimMode),
            InvalidDataMode::CastToEmpty => '',
            default => null,
        };
    }

    private function trim(string $value, TrimMode $mode): string
    {
        return match ($mode) {
            TrimMode::Full => trim($value),
            TrimMode::Start => ltrim($value),
            TrimMode::End => rtrim($value),
            default => $value,
        };
    }

    public function in(string $key, array $values): bool
    {
        return in_array($this->get($key), $values, true);
    }

    public function equal(string $key, string|object $compared): bool
    {
        $c = $compared;
        if (is_object($compared) && property_exists($compared, 'value') && isset($compared->value)) {
            $c = $compared->value;
        }

        return $this->get($key) === $c;
    }

    public function getOriginal(string $key): string|null
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