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
     * @var \Qafoo\PheanstalkCli\PrettyPrinterLocator
     */
    private $prettyPrinterLocator;

    /**
     * @param \Qafoo\PheanstalkCli\PheanstalkFactory $pheanstalkFactory
     * @param \Qafoo\PheanstalkCli\PrettyPrinterLocator $prettyPrinterLocator
     */
    public function __construct(PheanstalkFactory $pheanstalkFactory, PrettyPrinterLocator $prettyPrinterLocator)
    {
        // Needs to be assigned before configure() is called by parent::__construct()
        $this->prettyPrinterLocator = $prettyPrinterLocator;

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
            )->addOption(
                'pretty',
                'p',
                InputOption::VALUE_REQUIRED,
                sprintf(
                    'Pretty printer to use for payload (%s)',
                    implode(', ', $this->prettyPrinterLocator->listIdentifiers())
                ),
                $this->prettyPrinterLocator->defaultIdentifier()
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
                ),
                $this->prettyPrinterLocator->determinePrinter(
                    $input->getOption('pretty')
                )
            )
        );
    }

    /**
     * @param \Pheanstalk_Job $job
     * @param \Qafoo\PheanstalkCli\PrettyPrinter
     * @return string
     */
    private function formatOutput(Pheanstalk_Job $job, PrettyPrinter $prettyPrinter)
    {
        return implode(
            "\n",
            array(
                sprintf('ID: %s', $job->getId()),
                sprintf('Data:'),
                $prettyPrinter->format($job->getData())
            )
        );
    }
}
