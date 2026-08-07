<?php

namespace Lkt\Connectors\Enums;

enum MailSecurity: string
{
    case SSL = 'ssl';
    case TLS = 'tls';
}
