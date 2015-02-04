<?php

namespace Qafoo\PheanstalkCli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk_PheanstalkInterface;
use Pheanstalk_Job;

class PeekCommand extends Command
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
     * @var array
     */
    private $jobStateMap = array(
        'ready' => 'peekReady',
        'delayed' => 'peekDelayed',
        'buried' => 'peekBuried',
    );

    /**
     * @var string
     */
    private $defaultJobState= 'ready';

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
        $this->setName('peek')
            ->setDescription('Peek on the next job in a tube')
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
            )->addOption(
                'state',
                's',
                InputOption::VALUE_REQUIRED,
                sprintf(
                    'State of the job to peek (%s)',
                    implode(', ', array_keys($this->jobStateMap))
                ),
                $this->defaultJobState
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobState = $input->getOption('state');
        $this->assertInputStateValid($jobState);

        $peekMethod = $this->jobStateMap[$jobState];

        $output->writeln(
            $this->formatOutput(
                $this->pheanstalkFactory->create()->$peekMethod(
                    $input->getOption('tube')
                ),
                $this->prettyPrinterLocator->determinePrinter(
                    $input->getOption('pretty')
                )
            )
        );
    }

    /**
     * @param string $inputState
     * @throws \InvalidArgumentException if $inputState is not valid
     */
    private function assertInputStateValid($inputState)
    {
        if (!isset($this->jobStateMap[$inputState])) {
            throw new \InvalidArgumentException(
                sprintf('Invalid job state "%s", valid are: ', $inputState, $this->validStates())
            );
        }
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
