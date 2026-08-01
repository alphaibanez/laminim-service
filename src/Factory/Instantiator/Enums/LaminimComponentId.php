<?php

namespace Lkt\Factory\Instantiator\Enums;

enum LaminimComponentId: int
{
    case User = 1;
    case UserRole = 2;
    case AccessToken = 3;
    case AuthenticationLog = 4;
    case ContactReason = 5;
    case ContactRequest = 6;
    case Country = 7;
    case Currency = 8;
    case DateFormat = 9;
    case FileEntity = 10;
    case FileFormat = 11;
    case HttpRequestLog = 12;
    case Menu = 13;
    case MenuEntry = 14;
    case PendingMailing = 15;
    case ShoppingOrder = 16;
    case ShoppingOrderItem = 17;
    case ShoppingOrderPayment = 18;
    case ShoppingOrderStatusLog = 19;
    case Translation = 20;
    case WebElement = 21;
    case WebPage = 22;
    case WebPageCategory = 23;
    case WebPageMeta = 24;
    case ShoppingSubscription = 25;
    case ShoppingPrice = 26;
    case ShoppingTax = 27;
}