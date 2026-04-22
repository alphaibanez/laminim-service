<?php

namespace Lkt\Factory\Instantiator\Enums;

enum FieldFilterMode: string
{
    case greaterOrEqualThan = 'gte';
    case greaterThan = 'gt';
    case lowerOrEqualThan = 'lte';
    case lowerThan = 'lt';
    case notEqual = 'ne';
    case notLike = 'nl';
    case notBeginsLike = 'nbl';
    case notEndsLike = 'nel';
}