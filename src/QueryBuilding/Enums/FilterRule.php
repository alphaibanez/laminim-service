<?php

namespace Lkt\QueryBuilding\Enums;

final class FilterRule
{
    const empty = 'empty';
    const notEmpty = 'notEmpty';

    const isNull = 'isNull';
    const isNotNull = 'isNotNull';

    const greaterThanZero = 'greaterThanZero';
    const greaterOrEqualThanZero = 'greaterOrEqualThanZero';

    const lowerThanZero = 'lowerThanZero';
    const lowerOrEqualThanZero = 'lowerOrEqualThanZero';
}