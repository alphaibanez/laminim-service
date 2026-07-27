<?php

namespace Lkt\Config;

use Lkt\Enums\LaminimComponent;
use Lkt\Factory\Instantiator\ComponentId;
use Lkt\Factory\Instantiator\Enums\LaminimComponentId;

ComponentId::add(LaminimComponentId::User->value, LaminimComponent::User->value);
ComponentId::add(LaminimComponentId::UserRole->value, LaminimComponent::UserRole->value);
ComponentId::add(LaminimComponentId::AccessToken->value, LaminimComponent::AccessToken->value);
ComponentId::add(LaminimComponentId::AuthenticationLog->value, LaminimComponent::AuthenticationLog->value);
ComponentId::add(LaminimComponentId::ContactReason->value, LaminimComponent::ContactReason->value);
ComponentId::add(LaminimComponentId::ContactRequest->value, LaminimComponent::ContactRequest->value);
ComponentId::add(LaminimComponentId::Country->value, LaminimComponent::Country->value);
ComponentId::add(LaminimComponentId::Currency->value, LaminimComponent::Currency->value);
ComponentId::add(LaminimComponentId::DateFormat->value, LaminimComponent::DateFormat->value);
ComponentId::add(LaminimComponentId::FileEntity->value, LaminimComponent::FileEntity->value);
ComponentId::add(LaminimComponentId::FileFormat->value, LaminimComponent::FileFormat->value);
ComponentId::add(LaminimComponentId::HttpRequestLog->value, LaminimComponent::HTTPRequestLog->value);
ComponentId::add(LaminimComponentId::Menu->value, LaminimComponent::Menu->value);
ComponentId::add(LaminimComponentId::MenuEntry->value, LaminimComponent::MenuEntry->value);
ComponentId::add(LaminimComponentId::PendingMailing->value, LaminimComponent::PendingMail->value);
ComponentId::add(LaminimComponentId::ShoppingOrder->value, LaminimComponent::ShoppingOrder->value);
ComponentId::add(LaminimComponentId::ShoppingOrderItem->value, LaminimComponent::ShoppingOrderItem->value);
ComponentId::add(LaminimComponentId::ShoppingOrderPayment->value, LaminimComponent::ShoppingOrderPayment->value);
ComponentId::add(LaminimComponentId::ShoppingOrderStatusLog->value, LaminimComponent::ShoppingOrderStatusLog->value);
ComponentId::add(LaminimComponentId::ShoppingSubscription->value, LaminimComponent::ShoppingSubscription->value);
ComponentId::add(LaminimComponentId::ShoppingPrice->value, LaminimComponent::ShoppingPrice->value);
ComponentId::add(LaminimComponentId::Translation->value, LaminimComponent::Translation->value);
ComponentId::add(LaminimComponentId::WebElement->value, LaminimComponent::WebElement->value);
ComponentId::add(LaminimComponentId::WebPage->value, LaminimComponent::WebPage->value);
ComponentId::add(LaminimComponentId::WebPageCategory->value, LaminimComponent::WebPageCategory->value);
ComponentId::add(LaminimComponentId::WebPageMeta->value, LaminimComponent::WebPageMetas->value);
