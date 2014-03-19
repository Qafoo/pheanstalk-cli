<?php

namespace Qafoo\PheanstalkCli\PrettyPrinter;

use Qafoo\PheanstalkCli\PrettyPrinter;

use Qafoo\SerPretty\Parser;
use Qafoo\SerPretty\Writer;

class PhpSerializedPrettyPrinter extends PrettyPrinter
{
    /**
     * @var \Qafoo\SerPretty\Parser
     */
    private $parser;

    /**
     * @var \Qafoo\SerPretty\Writer
     */
    private $writer;

    /**
     * @param \Qafoo\SerPretty\Parser $parser
     * @param \Qafoo\SerPretty\Writer $writer
     */
    public function __construct(Parser $parser, Writer $writer)
    {
        $this->parser = $parser;
        $this->writer = $writer;
    }

    /**
     * @param mixed $payload
     * @return string
     */
    public function format($payload)
    {
        $node = $this->parser->parse($payload);
        return $this->writer->write($node);
    }
}
