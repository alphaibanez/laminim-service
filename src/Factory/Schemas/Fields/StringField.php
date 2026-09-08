<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Factory\Fields\Enums\EncryptAlgorithm;
use Lkt\Factory\Fields\Enums\StringFieldType;
use Lkt\Factory\Fields\Interfaces\NonRelationalField;
use Lkt\Factory\Fields\Traits\FieldWithLengthLimits;
use Lkt\Factory\Fields\Traits\FieldWithTrimMode;
use Lkt\Factory\Fields\Traits\FieldWithUniqueValue;
use Lkt\Factory\Schemas\Traits\FieldWithChoiceOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithEmptyDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithInvalidDataModeTrait;
use Lkt\Factory\Schemas\Traits\FieldWithJsonI18nStorageTrait;
use Lkt\Factory\Schemas\Traits\FieldWithMandatoryOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithNullOptionTrait;
use Lkt\Factory\Schemas\Traits\FieldWithSecureSeedTrait;

class StringField extends AbstractField implements NonRelationalField
{
//    use BaseFieldTrait,
//        FieldWithDefaultValue,
//        NonRelationalFieldInstantiation;

    use FieldWithNullOptionTrait,
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

    public static function i18n(string $name, string $column = ''): static
    {
        $ins = new static($name, $column);
        $ins->storeAsI18nJson = true;
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

    public function hasSHA256Encryption(): bool
    {
        return $this->encryptAlgorithm === EncryptAlgorithm::SHA256;
    }
}