<?php

namespace Lkt\Connectors\Enums;

enum RevolutWebhookEvent: string
{
    case OrderCompleted = 'ORDER_COMPLETED';
    case OrderAuthorised = 'ORDER_AUTHORISED';
    case OrderCanceled = 'ORDER_CANCELLED';
    case OrderFailed = 'ORDER_FAILED';
    case OrderIncrementalAuthorisationAuthorised = 'ORDER_INCREMENTAL_AUTHORISATION_AUTHORISED';
    case OrderIncrementalAuthorisationDeclined = 'ORDER_INCREMENTAL_AUTHORISATION_DECLINED';
    case OrderIncrementalAuthorisationFailed = 'ORDER_INCREMENTAL_AUTHORISATION_FAILED';
    case OrderIncrementalAuthorisationChanged = 'ORDER_PAYMENT_AUTHENTICATION_CHALLENGED';
    case OrderPaymentAuthenticated = 'ORDER_PAYMENT_AUTHENTICATED';
    case OrderPaymentDeclined = 'ORDER_PAYMENT_DECLINED';
    case OrderPaymentFailed = 'ORDER_PAYMENT_FAILED';
    case PayoutInitiated = 'PAYOUT_INITIATED';
    case PayoutCompleted = 'PAYOUT_COMPLETED';
    case PayoutFailed = 'PAYOUT_FAILED';
    case DisputeActionRequired = 'DISPUTE_ACTION_REQUIRED';
    case DisputeActionReview = 'DISPUTE_UNDER_REVIEW';
    case DisputeActionWon = 'DISPUTE_WON';
    case DisputeActionLost = 'DISPUTE_LOST';
    case SubscriptionInitiated = 'SUBSCRIPTION_INITIATED';
    case SubscriptionFinished = 'SUBSCRIPTION_FINISHED';
    case SubscriptionCancelled = 'SUBSCRIPTION_CANCELLED';
    case SubscriptionOverdue = 'SUBSCRIPTION_OVERDUE';
}