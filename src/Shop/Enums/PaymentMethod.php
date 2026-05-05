<?php

namespace Lkt\Shop\Enums;

enum PaymentMethod: int
{
    case Revolut = 1;
    case Stripe = 2;
    case Paypal = 3;
    case RedSys = 4;
}
