<?php

namespace Lkt\Config\Settings;

use Lkt\Users\Enums\UserAuthenticationMode;

class UserSettings
{
    /**
     * @laminim
     * Configure which field will be tested in order to authenticate users
     * It could be:
     *  - email
     *  - credential identifier
     *  - dynamic
     */
    public static UserAuthenticationMode $authMode = UserAuthenticationMode::Dynamic;

    /**
     * @laminim
     * Configure password secure seed
     */
    public static string $passwordSecureSeed = 'changeMe123$';
}