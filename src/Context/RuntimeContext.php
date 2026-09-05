<?php

namespace Lkt\Context;

use Lkt\Context\Enums\RuntimeEntryContext;
use Lkt\Context\Enums\RuntimeEntryPoint;

class RuntimeContext
{
    public static RuntimeEntryContext $entryContext = RuntimeEntryContext::None;
    public static RuntimeEntryPoint $entryPoint = RuntimeEntryPoint::Unknown;
}