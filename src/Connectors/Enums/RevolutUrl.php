<?php

namespace Lkt\Connectors\Enums;

enum RevolutUrl: string
{
    case MerchantAPI = 'https://merchant.revolut.com';
    case SandboxMerchantAPI = 'https://sandbox-merchant.revolut.com';
}
