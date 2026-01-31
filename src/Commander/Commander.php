<?php

namespace Lkt\Commander;

use Poliander\Cron\CronExpression;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

final class Commander
{
    private static array $consoleCommands = [];

    private static array $schedule = [];

    private static string $crontabCommand = 'php cmd';

    public static function register(Command $command): void
    {
        Commander::$consoleCommands[$command->getName()] = $command;
    }

    public static function schedule(Command $command, int|string $minute = '*', int|string $hour = '*', int|string $dayOfMonth = '*', int|string $month = '*', int|string $dayOfWeek = '*'): void
    {
        Commander::$consoleCommands[$command->getName()] = $command;
        Commander::$schedule[$command->getName()] = [$minute, $hour, $dayOfMonth, $month, $dayOfWeek];
    }

    public static function setCrontabCommand(string $command): void
    {
        static::$crontabCommand = $command;
    }

    /**
     * @throws \Exception
     */
    public static function run(): void
    {
        $application = Commander::getApplication();
        $application->run();
    }

    public static function getApplication(): Application
    {
        $application = new Application();

        foreach (Commander::$consoleCommands as $command) {
            $application->add($command);
        }

        return $application;
    }

    public static function getCrontabConfig(): array
    {
        $r = [];

        foreach (Commander::$schedule as $command => $config) {
            $caller = static::$crontabCommand;
            $r[] = "$config[0] $config[1] $config[2] $config[3] $config[4] $caller $command";
        }

        return $r;
    }

    public static function getExecutableCommands(): array
    {
        $r = [];

        foreach (Commander::$schedule as $command => $config) {
//            $caller = static::$crontabCommand;
            $expression = new CronExpression("$config[0] $config[1] $config[2] $config[3] $config[4]");
            if ($expression->isMatching()) {
                $r[] = static::$consoleCommands[$command];
//                $r[] = "$caller $command";
            }
        }

        return $r;
    }
}