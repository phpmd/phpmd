<?php

namespace PHPMD\Console;

abstract class Output implements OutputInterface
{
    /** @var int */
    private $verbosity;

    /**
     * @param int $verbosity
     */
    public function __construct($verbosity = self::VERBOSITY_NORMAL)
    {
        $this->verbosity = $verbosity;
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

    /**
     * @return int
     */
    public function getVerbosity()
    {
        return $this->verbosity;
    }

    /**
     * @param string $message
     */
    abstract protected function doWrite($message): void;
}
