<?php

namespace Lkt\QueryBuilding\Enums;

enum DateGroupByInterval: int
{
    case NotDefined = 0;
    case FifteenMinutes = 1;
    case ThirtyMinutes = 2;
    case OneHour = 3;
    case OneDay = 4;
    case OneWeek = 5;
    case OneMonth = 6;
    case OneYear = 7;
}