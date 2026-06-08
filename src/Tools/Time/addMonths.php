<?php

namespace Lkt\Tools\Time;

/**
 * Use a negative value in order to subtract
 * @param int $months
 * @param \DateTime|null $dateTime
 * @return \DateTime
 */
function addMonthsToDate(int $months = 1, \DateTime|null $dateTime = null): \DateTime
{
    if (!$dateTime) $dateTime = new \DateTime();
    return $dateTime->add(\DateInterval::createFromDateString("{$months} month"));
}