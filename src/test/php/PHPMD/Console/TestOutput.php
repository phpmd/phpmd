<?php

namespace PHPMD\Console;

class TestOutput extends Output
{
    /**
     * @param resource $stream
     */
    public function __construct(private $stream)
    {
        parent::__construct();
    }

    /**
     * @param string $message
     */
    protected function doWrite($message): void
    {
        fwrite($this->stream, $message);
    }

    public function getOutput(): string
    {
        fseek($this->stream, 0);

        return fread($this->stream, 1024) ?: '';
    }
}
