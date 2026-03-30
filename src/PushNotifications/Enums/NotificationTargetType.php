<?php

namespace Lkt\PushNotifications\Enums;

enum NotificationTargetType: int
{
    case AllUsers = 0;
    case SelectedUsers = 1;
    case SelectedAppRoles = 2;
    case SelectedAdminRoles = 3;
}
