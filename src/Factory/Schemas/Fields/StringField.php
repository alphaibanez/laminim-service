<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Enums\EncryptAlgorithm;
use Lkt\Factory\Fields\Enums\StringFieldType;
use Lkt\Factory\Fields\Interfaces\Field;
use Lkt\Factory\Fields\Traits\BaseFieldTrait;
use Lkt\Factory\Fields\Traits\FieldWithChoiceOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithDefaultValue;
use Lkt\Factory\Fields\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Fields\Traits\FieldWithJsonI18nStorageTrait;
use Lkt\Factory\Fields\Traits\FieldWithLengthLimits;
use Lkt\Factory\Fields\Traits\FieldWithMandatoryOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithNullOptionTrait;
use Lkt\Factory\Fields\Traits\FieldWithSecureSeedTrait;
use Lkt\Factory\Fields\Traits\FieldWithTrimMode;
use Lkt\Factory\Fields\Traits\FieldWithUniqueValue;
use Lkt\Factory\Schemas\Schema;

class StringField implements Field
{
    use BaseFieldTrait,
        FieldWithDefaultValue,
        FieldWithNullOptionTrait,
        FieldWithJsonI18nStorageTrait,
        FieldWithMandatoryOptionTrait,
        FieldWithInvalidDataModeTrait,
        FieldWithEmptyDataModeTrait,
        FieldWithUniqueValue,
        FieldWithTrimMode,
        FieldWithLengthLimits,
        FieldWithChoiceOptionTrait,
        FieldWithSecureSeedTrait;

    protected StringFieldType $fieldType = StringFieldType::String;
    protected EncryptAlgorithm $encryptAlgorithm = EncryptAlgorithm::None;

    protected array $concatenatedFields = [];
    protected string $separator = '';

    public static function i18n(string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->storeAsI18nJson = true;
        return $ins;
    }

    /**
     * Retrieve data from a given i18n
     * Use field names between brackets in order to parse i18n key:
     *
     * For example: 'role.{id}' will become into i18n 'role.1' if the instance has id = 1
     */
    public static function translate(string $name, string $i18nKey = ''): static
    {
        $ins = new static($name, $i18nKey);
        $ins->fieldType = StringFieldType::Translate;
        return $ins;
    }

    public static function url(string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->fieldType = StringFieldType::Url;
        return $ins;
    }

    public static function email(string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->fieldType = StringFieldType::Email;
        return $ins;
    }

    public static function html(string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->fieldType = StringFieldType::HTML;
        return $ins;
    }

    public static function concat(string $name, array $fields, string $separator): static
    {
        $ins = new static($name, '');
        $ins->fieldType = StringFieldType::Concat;
        $ins->concatenatedFields = $fields;
        $ins->separator = $separator;
        return $ins;
    }

    public static function sha256(string $secureSeed, string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->encryptAlgorithm = EncryptAlgorithm::SHA256;
        $ins->secureSeed = $secureSeed;
        return $ins;
    }

    public static function sha256Hash(string $secureSeed, string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->encryptAlgorithm = EncryptAlgorithm::SHA256Hash;
        $ins->secureSeed = $secureSeed;
        return $ins;
    }

    public function isEmail(): bool
    {
        return $this->fieldType === StringFieldType::Email;
    }

    public function isHTML(): bool
    {
        return $this->fieldType === StringFieldType::HTML;
    }

    public function isEncrypted(): bool
    {
        return $this->encryptAlgorithm !== EncryptAlgorithm::None;
    }

    public function isHashMode(): bool
    {
        return $this->encryptAlgorithm === EncryptAlgorithm::SHA256Hash;
    }

    public function isTranslation(): bool
    {
        return $this->fieldType === StringFieldType::Translate;
    }

    public function isConcatenation(): bool
    {
        return $this->fieldType === StringFieldType::Concat;
    }

    public function hasSHA256Encryption(): bool
    {
        return $this->encryptAlgorithm === EncryptAlgorithm::SHA256;
    }

    public function getConcatenatedFields(): array
    {
        return $this->concatenatedFields;
    }

    public function getConcatenatedFieldsAsString(Schema $schema): string
    {
        $r = [];
        foreach ($this->concatenatedFields as $field) {
            $f = $schema->getField($field);
            $r[] = "'{$f->getColumn()}'";
        }
        $r = implode(',', $r);
        return "[{$r}]";
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }
}