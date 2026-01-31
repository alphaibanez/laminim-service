<?php

namespace Lkt\Enums;

enum TimeInSeconds: int
{
    case OneDay = 86400;
    case OneWeek = 604800;
    case OneMonth = 2419200;
    case OneYear = 31536000;
}
