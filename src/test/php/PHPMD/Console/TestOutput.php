<?php

namespace PHPMD\Console;

class TestOutput extends Output
{
    /** @var resource */
    private $stream;

    public function __construct()
    {
        parent::__construct();
        $this->stream = fopen('php://memory', 'w+b');
    }

    /**
     * @param string $message
     * @return void
     */
    protected function doWrite($message)
    {
        fwrite($this->stream, $message);
    }

    public function getOutput()
    {
        fseek($this->stream, 0);
        return fread($this->stream, 1024);
    }
}
