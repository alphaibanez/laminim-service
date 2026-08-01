<?php

namespace Lkt\Shop\Enums;

enum TaxTarget: int
{
    case NaturalPerson = 0;
    case LegalPerson = 1;
    case Company = 2;
}
