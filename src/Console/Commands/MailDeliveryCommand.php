<?php

namespace Lkt\Console\Commands;


use Lkt\Connectors\MailConnector;
use Lkt\Generated\LktPendingMailOrderBy;
use Lkt\Instances\LktPendingMail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Lkt\Tools\Parse\clearInput;

class MailDeliveryCommand extends Command
{
    protected static $defaultName = 'lkt:mail-delivery';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Automatically delivers pending mails')
            ->addArgument('from', InputArgument::OPTIONAL, 'Custom mail from')
            ->addArgument('connector', InputArgument::OPTIONAL, 'Custom mail driver')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Starting mailing");

        $from = clearInput($input->getArgument('from'));
        $connector = clearInput($input->getArgument('connector'));
        if (!$connector) $connector = 'default';


        $connector = MailConnector::get($connector);

        $mails = LktPendingMail::getMany(LktPendingMail::getQueryBuilder()->orderBy(LktPendingMailOrderBy::priorityDESC()->andIdDESC()));

        foreach ($mails as $mail) {
            if ($mail->send($connector, $from) === true) $mail->delete();
        }
        return 1;
    }
}