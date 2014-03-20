<?php

namespace Qafoo\PheanstalkCli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk_PheanstalkInterface;
use Pheanstalk_Response_ArrayResponse;

class StatsJobCommand extends Command
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
        $this->setName('stats-job')
            ->setDescription('Show stats on a specific job')
            ->addArgument(
                'job-id',
                InputArgument::REQUIRED,
                'Job ID'
            );
    }

    /**
     * @param \Symfony\Command\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobId = $input->getArgument('job-id');

        $pheanstalk = $this->pheanstalkFactory->create();
        $output->writeln(
            $this->formatOutput(
                $pheanstalk->statsJob(
                    $pheanstalk->peek($jobId)
                )
            )
        );
    }

    /**
     * @param \Pheanstalk_Response_ArrayResponse $stats
     * @return string
     */
    private function formatOutput(Pheanstalk_Response_ArrayResponse $stats)
    {
        $formattedStats = array();
        foreach ($stats as $key => $value) {
            $formattedStats[] = sprintf(
                '%25s: %-10s',
                $key,
                $value
            );
        }
        return implode("\n", $formattedStats);
    }
}
