<?php

namespace Lkt\Shop\Enums;

enum TaxType: int
{
    case PercentualAdd = 0;
    case PercentualReverseCalc = 1;
    case FixedAmount = 2;
    case Exempt = 3;
}
