<?php

namespace Qafoo\PheanstalkCli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk_PheanstalkInterface;
use Pheanstalk_Job;

class PeekReadyCommand extends Command
{
    /**
     * @var \Qafoo\PheanstalkCli\PheanstalkFactory
     */
    private $pheanstalkFactory;

    /**
     * @param \Qafoo\PheanstalkCli\PheanstalkFactory $pheanstalkFactory
     */
    public function __construct(PheanstalkFactory $pheanstalkFactory)
    {
        parent::__construct();

        $this->pheanstalkFactory = $pheanstalkFactory;
    }

    protected function configure()
    {
        $this->setName('peek-ready')
            ->setDescription('Peek on the next ready job')
            ->addOption(
                'tube',
                't',
                InputOption::VALUE_REQUIRED,
                'The tube to peek from',
                'default'
            );
    }

    /**
     * @param \Symfony\Command\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            $this->formatOutput(
                $this->pheanstalkFactory->create()->peekReady(
                    $input->getOption('tube')
                )
            )
        );
    }

    /**
     * @param \Pheanstalk_Job $job
     * @return string
     */
    private function formatOutput(Pheanstalk_Job $job)
    {
        return implode(
            "\n",
            array(
                sprintf('ID: %s', $job->getId()),
                sprintf('Data:'),
                $job->getData()
            )
        );
    }
}
