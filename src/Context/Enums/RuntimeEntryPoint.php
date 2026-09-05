<?php

namespace Lkt\Context\Enums;

enum RuntimeEntryPoint
{
    case Unknown;
    case CommandLineInterface;
    case HTTP;
}