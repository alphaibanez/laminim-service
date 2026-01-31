<?php

namespace Lkt\Console\Commands;

use Lkt\Commander\Commander;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCrontabCommand extends Command
{
    protected static $defaultName = 'lkt:show:crontab';

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

        $data = Commander::getCrontabConfig();
        $data = [...$data, "\n\n"];
        $output->writeln(implode("\n\n", $data));
        return 1;
    }
}