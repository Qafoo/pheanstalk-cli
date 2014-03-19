<?php

namespace Qafoo\PheanstalkCli\PrettyPrinter;

use Qafoo\PheanstalkCli\PrettyPrinter;

class NonePrettyPrinter extends PrettyPrinter
{
    /**
     * @param mixed $payload
     * @return string
     */
    public function format($payload)
    {
        return $payload;
    }
}
