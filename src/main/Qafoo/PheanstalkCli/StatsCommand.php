<?php

namespace Qafoo\PheanstalkCli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk_PheanstalkInterface;
use Pheanstalk_Response_ArrayResponse;

class StatsCommand extends Command
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
        $this->setName('stats')
            ->setDescription('Show stats');
    }

    /**
     * @param \Symfony\Command\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            $this->formatOutput(
                $this->pheanstalkFactory->create()->stats()
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
