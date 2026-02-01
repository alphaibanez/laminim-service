<?php

namespace Lkt\Http\Enums;

enum NotificationCategory: string
{
    case Toast = 'toast';
    case Message = 'message';
    case Redirect = 'redirect';
    case Reload = 'reload';
    case SyncAppResource = 'sync-app-resource';
}
