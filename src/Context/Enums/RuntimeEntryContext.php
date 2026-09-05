<?php

namespace Lkt\Context\Enums;

enum RuntimeEntryContext
{
    case None;
    case CodeGeneration;
    case APP;
    case API;
}