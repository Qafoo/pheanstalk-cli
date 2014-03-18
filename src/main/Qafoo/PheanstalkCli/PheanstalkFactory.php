<?php

namespace Qafoo\PheanstalkCli;

use \Pheanstalk_PheanstalkInterface;
use \Pheanstalk_Pheanstalk;

class PheanstalkFactory
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @param string $host
     * @param int $port
     */
    public function __construct($host = '127.0.0.1', $port = Pheanstalk_PheanstalkInterface::DEFAULT_PORT)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return \Pheanstalk_PheanstalkInterface
     */
    public function create()
    {
        return new Pheanstalk_Pheanstalk(
            $this->host,
            $this->port
        );
    }
}
