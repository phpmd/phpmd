<?php

namespace PHPMD\Utility;

/**
 * A basic write-to-stream output class. Follows a partial implementation of Symfony's OutputInterface to allow future drop-in replacement
 * by the Console package.
 */
class Output
{
    const VERBOSITY_QUIET        = 16;
    const VERBOSITY_NORMAL       = 32;
    const VERBOSITY_VERBOSE      = 64;
    const VERBOSITY_VERY_VERBOSE = 128;
    const VERBOSITY_DEBUG        = 256;

    /** @var resource */
    private $stream;

    /** @var int */
    private $verbosity;

    /**
     * @param resource $stream
     * @param int      $verbosity
     */
    public function __construct($stream, $verbosity = self::VERBOSITY_NORMAL)
    {
        $this->stream    = $stream;
        $this->verbosity = $verbosity;
    }

    /**
     * @param string|string[] $messages
     * @param bool            $newline
     * @param int             $options A bitmask of options (one of the VERBOSITY constants),
     *                                 0 is considered the same as self::VERBOSITY_NORMAL
     * @return void
     */
    public function write($messages, $newline = false, $options = self::VERBOSITY_NORMAL)
    {
        if (is_array($messages) === false) {
            $messages = array($messages);
        }

        $verbosities = self::VERBOSITY_QUIET
            | self::VERBOSITY_NORMAL
            | self::VERBOSITY_VERBOSE
            | self::VERBOSITY_VERY_VERBOSE
            | self::VERBOSITY_DEBUG;
        $verbosity   = $verbosities & $options ?: self::VERBOSITY_NORMAL;

        if ($verbosity > $this->getVerbosity()) {
            return;
        }

        foreach ($messages as $message) {
            fwrite($this->stream, $message . ($newline ? "\n" : ""));
        }
    }

    /**
     * @param string|string[] $messages
     * @param int             $options A bitmask of options (one of the VERBOSITY constants),
     *                                 0 is considered the same as self::VERBOSITY_NORMAL
     * @return void
     */
    public function writeln($messages, $options = self::VERBOSITY_NORMAL)
    {
        $this->write($messages, true, $options);
    }

    /**
     * @param int $level
     * @return void
     */
    public function setVerbosity($level)
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
}
