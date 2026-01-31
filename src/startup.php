<?php

namespace Lkt\Commander;

use Lkt\Console\Commands\MakeCrontabCommand;
use Lkt\Console\Commands\RunCrontabCommand;
use Lkt\Console\Commands\ShowCrontabCommand;
use Lkt\Phinx\PhinxConfigurator;
use Lkt\Translations\Translations;


function __(string $key = '', string $lang = null)
{
    return Translations::get($key, $lang);
}

function addLocalePath(string $lang, string $path): void
{
    Translations::addLocalePath($lang, $path);
}

/**
 * Load Schemas
 */
require_once __DIR__ . '/Config/Schemas/LktTranslationsSchema.php';

if (php_sapi_name() == 'cli') {
    PhinxConfigurator::addMigrationPath(__DIR__ . '/../database/migrations');
}

if (php_sapi_name() == 'cli') {
    Commander::register(new MakeCrontabCommand());
    Commander::register(new RunCrontabCommand());
    Commander::register(new ShowCrontabCommand());
}