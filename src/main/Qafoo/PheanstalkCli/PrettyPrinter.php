<?php

namespace Qafoo\PheanstalkCli;

abstract class PrettyPrinter
{
    /**
     * Returns a pretty printed version of $payload
     *
     * @param mixed $payload
     * @return string
     */
    abstract public function format($payload);
}
