<?php

namespace Lkt\Factory\Schemas\Fields;

/**
 * @deprecated
 * Use IntegerField::identifier insted
 */
class IdField extends IntegerField
{
    protected bool $isIdentifier = true;
}