<?php

namespace Qafoo\PheanstalkCli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk_PheanstalkInterface;
use Pheanstalk_Job;

class DeleteCommand extends Command
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
        $this->setName('delete')
            ->setDescription('Delete a job')
            ->addArgument(
                'job-id',
                InputArgument::REQUIRED,
                'Job ID'
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobId = $input->getArgument('job-id');

        $pheanstalk = $this->pheanstalkFactory->create();
        $pheanstalk->delete(
            $pheanstalk->peek($jobId)
        );

        $output->writeln(
            sprintf('<info>Successfully deleted job %s</info>', $jobId)
        );
    }
}
