#!/usr/bin/env php
<?php

namespace Lkt\Console;

use Lkt\Context\Enums\RuntimeEntryPoint;
use Lkt\Context\RuntimeContext;

ini_set('display_errors', 0);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

session_start();

require __DIR__ .'/vendor/autoload.php';

RuntimeContext::$entryPoint = RuntimeEntryPoint::CommandLineInterface;

\Lkt\Commander\Commander::run();