<?php

namespace Lkt\Shop\Enums;

enum PriceType: int
{
    case Override = 0;
    case CustomTaxes = 1;
}
