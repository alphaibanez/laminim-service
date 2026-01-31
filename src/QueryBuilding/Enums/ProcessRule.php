<?php

namespace Lkt\QueryBuilding\Enums;

final class ProcessRule
{
    const ignore = 'ignore';
    const equal = 'equal';

    const in = 'in';
    const notIn = 'notIn';

    const beginsLike = 'beginsLike';
    const endsLike = 'endsLike';
    const like = 'like';

    const notBeginsLike = 'notBeginsLike';
    const notEndsLike = 'notEndsLike';
    const notLike = 'notLike';

    const greaterOrEqualThan = 'greaterOrEqualThan';
    const greaterThan = 'greaterThan';
    const notGreaterOrEqualThan = 'notGreaterOrEqualThan';
    const notGreaterThan = 'notGreaterThan';

    const lowerOrEqualThan = 'lowerOrEqualThan';
    const lowerThan = 'lowerThan';
    const notLowerOrEqualThan = 'notLowerOrEqualThan';
    const notLowerThan = 'notLowerThan';
}