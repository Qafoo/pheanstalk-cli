<?php

namespace Qafoo\PheanstalkCli;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk_PheanstalkInterface;
use Pheanstalk_Pheanstalk;

class Application extends BaseApplication
{
    const DEFAULT_HOST = '127.0.0.1';

    const DEFAULT_PORT = 11300;

    /**
     * @var \Qafoo\PheanstalkCli\PheanstalkFactory
     */
    private $pheanstalkFactory;

    public function __construct($version)
    {
        parent::__construct('pheanstalk-cli, a beanstalk cli', $version);

        $this->pheanstalkFactory = new PheanstalkFactory();

        $this->getDefinition()->addOption(
            new InputOption(
                'host',
                null,
                InputOption::VALUE_OPTIONAL,
                'Beanstalkd host.',
                self::DEFAULT_HOST
            )
        );
        $this->getDefinition()->addOption(
            new InputOption(
                'port',
                null,
                InputOption::VALUE_OPTIONAL,
                'Beanstalkd port.',
                self::DEFAULT_PORT
            )
        );

        $this->add(new StatsCommand($this->pheanstalkFactory));
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->pheanstalkFactory->setHost(
            $input->getParameterOption(
                '--host',
                self::DEFAULT_HOST
            )
        );
        $this->pheanstalkFactory->setPort(
            $input->getParameterOption(
                '--port',
                self::DEFAULT_PORT
            )
        );

        return parent::doRun($input, $output);
    }
}
