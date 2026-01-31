<?php

namespace Lkt\Console\Commands;

use Lkt\Commander\Commander;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCrontabCommand extends Command
{
    protected static $defaultName = 'lkt:run:crontab';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Automatically generates a fresh crontab file')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $data = Commander::getExecutableCommands();

        foreach ($data as $command) {
            $output->writeln("Executing command: {$command->getName()}");
            $command->execute($input, $output);
        }

        return 1;
    }
}