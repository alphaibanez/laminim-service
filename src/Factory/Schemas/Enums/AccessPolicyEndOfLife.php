<?php

namespace Lkt\Factory\Schemas\Enums;

/**
 * Every AbstractInstance has a native integration with AccessPolicy.
 * This usage can be one-time or until you desire to remove.
 */
enum AccessPolicyEndOfLife: int
{
    case UntilUpdated = 1;
    case UntilNextWrite = 2;
    case UntilNextRead = 3;
}