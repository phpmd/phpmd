<?php

namespace PHPMD\Console;

final class StreamOutput extends Output
{
    /**
     * @param resource $stream
     */
    public function __construct(
        private $stream,
        int $verbosity = self::VERBOSITY_NORMAL,
    ) {
        parent::__construct($verbosity);
    }

    /**
     * @inheritDoc
     */
    protected function doWrite($message): void
    {
        fwrite($this->stream, $message);
    }
}
