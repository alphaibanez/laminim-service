<?php

namespace Lkt\Factory\Schemas\Fields;

class IdField extends IntegerField
{
    const TYPE = 'id';

    protected bool $isIdentifier = true;
}