<?php

namespace Qafoo\PheanstalkCli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk_PheanstalkInterface;
use Pheanstalk_Response_ArrayResponse;

class ListTubesCommand extends Command
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
        $this->setName('list-tubes')
            ->setDescription('List all known tubes');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            $this->formatOutput(
                $this->pheanstalkFactory->create()->listTubes()
            )
        );
    }

    /**
     * @param array $tubes
     * @return string
     */
    private function formatOutput(array $tubes)
    {
        return implode("\n", $tubes);
    }
}
