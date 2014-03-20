<?php

namespace Qafoo\PheanstalkCli;

class PrettyPrinterLocator
{
    /**
     * @var \Qafoo\PheanstalkCli\PrettyPrinter[]
     */
    private $prettyPrinters = array();

    /**
     * @param \Qafoo\PheanstalkCli\PrettyPrinter[] $prettyPrinters
     */
    public function __construct(array $prettyPrinters = array())
    {
        foreach ($prettyPrinters as $identifier => $printer) {
            $this->registerPrinter($identifier, $printer);
        }
    }

    /**
     * @param string $identifier
     * @param \Qafoo\PheanstalkCli\PrettyPrinter $printer
     */
    public function registerPrinter($identifier, PrettyPrinter $printer)
    {
        $this->prettyPrinters[$identifier] = $printer;
    }

    /**
     * @return string[]
     */
    public function listIdentifiers()
    {
        return array_keys($this->prettyPrinters);
    }

    /**
     * @return string
     */
    public function defaultIdentifier()
    {
        $identifiers = $this->listIdentifiers();
        return $identifiers[0];
    }

    /**
     * @param string $identifier
     * @return \Qafoo\PheanstalkCli\PrettyPrinter
     * @throws \RuntimeException if no printer for $identifier was found
     */
    public function determinePrinter($identifier)
    {
        if (!isset($this->prettyPrinters[$identifier])) {
            throw new \RuntimeException(
                "Printer with identifier '{$identifier}' not found."
            );
        }
        return $this->prettyPrinters[$identifier];
    }
}
