<?php

namespace Lkt;

use Lkt\Commander\Commander;
use Lkt\Console\Commands\GenerateCommand;
use Lkt\Console\Commands\MailDeliveryCommand;
use Lkt\Console\Commands\MakeCrontabCommand;
use Lkt\Console\Commands\RunCrontabCommand;
use Lkt\Console\Commands\SetupTranslationsCommand;
use Lkt\Console\Commands\ShowCrontabCommand;
use Lkt\Phinx\PhinxConfigurator;
use Lkt\Translations\Translations;
use function Lkt\Tools\Requiring\requireFiles;

require_once __DIR__ . '/Tools/Requiring/requireFiles.php';

requireFiles([
    // Load Tools
    __DIR__ . '/Tools/Arrays/*.php',
    __DIR__ . '/Tools/Color/*.php',
    __DIR__ . '/Tools/Csv/*.php',
    __DIR__ . '/Tools/Debug/*.php',
    __DIR__ . '/Tools/Enums/*.php',
    __DIR__ . '/Tools/Export/*.php',
    __DIR__ . '/Tools/Pagination/*.php',
    __DIR__ . '/Tools/Parse/*.php',
    __DIR__ . '/Tools/Strings/*.php',
    __DIR__ . '/Tools/System/*.php',
    __DIR__ . '/Tools/Time/*.php',
    __DIR__ . '/Tools/Url/*.php',
    __DIR__ . '/Tools/Xml/*.php',

    // Load Factory Schemas
    __DIR__ . '/Config/Schemas/*.php',
    __DIR__ . '/WebPages/functions/*.php',
    __DIR__ . '/Mailing/functions.php',
]);


function __(string $key = '', string $lang = null)
{
    return Translations::get($key, $lang);
}

function addLocalePath(string $lang, string $path): void
{
    Translations::addLocalePath($lang, $path);
}

if (php_sapi_name() == 'cli') {
    PhinxConfigurator::addMigrationPath(__DIR__ . '/../database/migrations');

    Commander::register(new GenerateCommand());
    Commander::register(new MailDeliveryCommand());
    Commander::register(new MakeCrontabCommand());
    Commander::register(new RunCrontabCommand());
    Commander::register(new SetupTranslationsCommand());
    Commander::register(new ShowCrontabCommand());
}