<?php

namespace Lkt\Factory\Schemas\Exceptions;

use Exception;

class InvalidCompositionConfigException extends Exception
{
    public function __construct($message = '', $val = 0, Exception $old = null)
    {
        if (!$message) {
            $message = 'InvalidCompositionConfigException: An schema component cannot be empty';
        }
        parent::__construct($message, $val, $old);
    }

    public static function stringFieldNamePointingToNonRelatedField(string $component, string $fieldName): static
    {
        return new static("InvalidCompositionConfigException: '{$component}' component doesn't have a related/foreign key field named '{$fieldName}'");
    }
}