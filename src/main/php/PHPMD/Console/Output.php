<?php

namespace PHPMD\Console;

abstract class Output implements OutputInterface
{
    public function __construct(
        private int $verbosity = self::VERBOSITY_NORMAL,
    ) {
    }

    /**
     * @param string|string[] $messages
     * @param bool            $newline
     * @param int             $options A bitmask of options (one of the VERBOSITY constants),
     *                                 0 is considered the same as self::VERBOSITY_NORMAL
     */
    public function write($messages, $newline = false, $options = self::VERBOSITY_NORMAL): void
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }

        $verbosities = self::VERBOSITY_QUIET
            | self::VERBOSITY_NORMAL
            | self::VERBOSITY_VERBOSE
            | self::VERBOSITY_VERY_VERBOSE
            | self::VERBOSITY_DEBUG;
        $verbosity = $verbosities & $options ?: self::VERBOSITY_NORMAL;

        if ($verbosity > $this->getVerbosity()) {
            return;
        }

        foreach ($messages as $message) {
            $this->doWrite($message . ($newline ? "\n" : ''));
        }
    }

    /**
     * @param string|string[] $messages
     * @param int             $options A bitmask of options (one of the VERBOSITY constants),
     *                                 0 is considered the same as self::VERBOSITY_NORMAL
     */
    public function writeln($messages, $options = self::VERBOSITY_NORMAL): void
    {
        $this->write($messages, true, $options);
    }

    /**
     * @param int $level
     */
    public function setVerbosity($level): void
    {
        $this->verbosity = $level;
    }

    public function getVerbosity(): int
    {
        return $this->verbosity;
    }

    abstract protected function doWrite(string $message): void;
}
