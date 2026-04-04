<?php

namespace Lkt\Config\Settings;

class ContactSettings
{
    /**
     * @laminim
     * Configure how much contact request can be delivered by a certain IP
     */
    public static int $maxRequestPerIp = 3;

    /**
     * @laminim
     * Configure the time period where the maxRequestsPerIp it's gonna be checked
     */
    public static int $maxRequestPeriod = 3;
}