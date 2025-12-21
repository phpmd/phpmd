<?php

namespace PHPMD\Console;

class StreamOutput extends Output
{
    /** @var resource */
    private $stream;

    /**
     * @param resource $stream
     */
    public function __construct($stream, $verbosity = self::VERBOSITY_NORMAL)
    {
        parent::__construct($verbosity);
        $this->stream = $stream;
    }

    /**
     * @inheritDoc
     */
    protected function doWrite($message)
    {
        fwrite($this->stream, $message);
    }
}
