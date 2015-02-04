<?php

namespace Qafoo\PheanstalkCli;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Pheanstalk_PheanstalkInterface;
use Pheanstalk_Job;

/**
 * @deprecated
 */
class PeekReadyCommand extends PeekCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('peek-ready');
        $this->setDescription('Deprecated, use "peek" instead');
    }
}
