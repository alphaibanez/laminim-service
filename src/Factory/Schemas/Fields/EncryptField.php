<?php

namespace Lkt\Factory\Schemas\Fields;

use Lkt\Attributes\Deprecated;
use Lkt\Factory\Fields\Interfaces\NonRelationalField;

/**
 * @deprecated Use StringField::sha256 or StringField::sha256Hash instead
 */
#[Deprecated]
class EncryptField extends StringField implements NonRelationalField
{
}