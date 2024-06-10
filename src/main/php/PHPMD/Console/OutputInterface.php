<?php

namespace PHPMD\Console;

/**
 * A basic OutputInterface that follows a partial implementation of Symfony's OutputInterface to allow future drop-in
 * replacement by the Console package.
 */
interface OutputInterface
{
    final public const VERBOSITY_QUIET = 16;
    final public const VERBOSITY_NORMAL = 32;
    final public const VERBOSITY_VERBOSE = 64;
    final public const VERBOSITY_VERY_VERBOSE = 128;
    final public const VERBOSITY_DEBUG = 256;

    /**
     * @param string|string[] $messages
     * @param bool            $newline
     * @param int             $options A bitmask of options (one of the VERBOSITY constants),
     *                                 0 is considered the same as self::VERBOSITY_NORMAL
     */
    public function write($messages, $newline = false, $options = self::VERBOSITY_NORMAL): void;

    /**
     * @param string|string[] $messages
     * @param int             $options A bitmask of options (one of the VERBOSITY constants),
     *                                 0 is considered the same as self::VERBOSITY_NORMAL
     */
    public function writeln($messages, $options = self::VERBOSITY_NORMAL): void;

    /**
     * @param int $level
     */
    public function setVerbosity($level): void;

    public function getVerbosity(): int;
}
