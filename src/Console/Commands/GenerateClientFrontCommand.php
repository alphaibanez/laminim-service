<?php

namespace Lkt\Console\Commands;

use Lkt\CodeMaker\ClientFrontMaker;
use Lkt\Context\Enums\RuntimeEntryContext;
use Lkt\Context\RuntimeContext;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GenerateClientFrontCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('lkt:make:client')

            // the short description shown while running "php bin/console list"
            ->setDescription('Automatically generates Laminim-client ready to use code based on the configuration')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        RuntimeContext::$entryContext = RuntimeEntryContext::CodeGeneration;

        $maker = ClientFrontMaker::getInstance();
        $maker->generate();
        return 1;
    }
}