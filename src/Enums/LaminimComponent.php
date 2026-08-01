<?php

namespace Lkt\Enums;

enum LaminimComponent: string
{
    case AccessToken = 'lkt-access-token';
    case AuthenticationLog = 'lkt-authentication-log';
    case ContactReason = 'lkt-contact-reason';
    case ContactRequest = 'lkt-contact-request';
    case Country = 'lkt-country';
    case CountryState = 'lkt-country-state';
    case Currency = 'lkt-currency';
    case DateFormat = 'lkt-date-format';
    case FileEntity = 'lkt-file-entity';
    case FileFormat = 'lkt-file-format';
    case HTTPRequestLog = 'lkt-http-request-log';
    case MenuEntry = 'lkt-menu-entry';
    case Menu = 'lkt-menu';
    case MenuPivotEntry = 'lkt-menu-pivot-entry';
    case MenuEntryPivotMenuEntry = 'lkt-menu-entry-pivot-entry';
    case PendingMail = 'lkt-pending-mail';
    case PushDelivery = 'lkt-push-delivery';
    case PushDevice = 'lkt-push-device';
    case PushNotification = 'lkt-push-notification';
    case ShoppingCoupon = 'lkt-shopping-coupon';
    case ShoppingOrder = 'lkt-shopping-order';
    case ShoppingOrderItem = 'lkt-shopping-order-item';
    case ShoppingOrderPayment = 'lkt-shopping-order-payment';
    case ShoppingOrderStatusLog = 'lkt-shopping-order-status-log';
    case ShoppingPrice = 'lkt-shopping-price';
    case ShoppingTax = 'lkt-shopping-tax';
    case ShoppingSubscription = 'lkt-shopping-subscription';
    case ShoppingOrderPivotShoppingCoupon = 'lkt-shopping-order-pivot-coupon';
    case ShoppingOrderPivotShoppingSubscription = 'lkt-shopping-order-pivot-subscription';
    case Translation = 'lkt-i18n';
    case UserRole = 'lkt-user-role';
    case User = 'lkt-user';
    case WebElement = 'lkt-web-element';
    case WebPage = 'lkt-web-page';
    case WebPageCategory = 'lkt-web-page-category';
    case WebPageMetas = 'lkt-web-page-metas';
}