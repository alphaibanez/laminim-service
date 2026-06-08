<?php

namespace Lkt\Tools\Time;

/**
 * Use a negative value in order to subtract
 * @param int $days
 * @param \DateTime|null $dateTime
 * @return \DateTime
 */
function addDaysToDate(int $days = 1, \DateTime|null $dateTime = null): \DateTime
{
    if (!$dateTime) $dateTime = new \DateTime();
    return $dateTime->add(\DateInterval::createFromDateString("{$days} day"));
}