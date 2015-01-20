<?php

namespace Qafoo\PheanstalkCli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('Show stats')
            ->addOption(
                'tube',
                't',
                InputOption::VALUE_REQUIRED,
                'The tube to display stats for',
                null
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getOption('tube');

        $output->writeln(
            $this->formatOutput(
                ($tube === null
                    ? $this->pheanstalkFactory->create()->stats()
                    : $this->pheanstalkFactory->create()->statsTube($tube))
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
