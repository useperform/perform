<?php

namespace Perform\MailingListBundle\Command;

use Perform\MailingListBundle\SubscriberManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ProcessQueueCommand extends Command
{
    protected $subManager;

    public function __construct(SubscriberManager $subManager)
    {
        parent::__construct();
        $this->subManager = $subManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Process the subscriber queue table')
            ->setHelp(<<<EOF

The <info>%command.name%</info> command takes each entry in the
subscriber queue and passes it to the relevant mailing list connector.

No output is shown on default verbosity settings; use -vv or -vvv to
see information as each entry is processed.

Use the <info>--batch-size</info> option to define how many queue entries to process
at once, to save memory.
EOF
            )
            ->addOption('batch-size', '', InputOption::VALUE_REQUIRED, 'The number of table rows to process at once.', 100)
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->subManager->processQueue($input->getOption('batch-size'));

        $output->writeln('Processed subscriber queue.');
    }
}
