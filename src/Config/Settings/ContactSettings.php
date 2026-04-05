<?php

namespace Lkt\Config\Settings;

class ContactSettings
{
    /**
     * @laminim
     * Configure how much contact request can be delivered by a certain IP
     */
    public static int|false $maxRequestPerIp = 3;

    /**
     * @laminim
     * Configure the time period where the maxRequestsPerIp it's gonna be checked
     * It's expressed in seconds
     * The greater the number, the greater the restriction
     */
    public static int $maxRequestPeriod = 86400;
}