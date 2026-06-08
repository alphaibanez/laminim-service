<?php

namespace Lkt\Shop\Enums;

enum PriceCriteria: int
{
    case ByCountry = 0;
    case ShippingArea = 1;
}
