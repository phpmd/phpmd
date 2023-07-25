<?php

namespace PHPMD\Console;

/**
 * A basic OutputInterface that follows a partial implementation of Symfony's OutputInterface to allow future drop-in
 * replacement by the Console package.
 */
interface OutputInterface
{
    const VERBOSITY_QUIET        = 16;
    const VERBOSITY_NORMAL       = 32;
    const VERBOSITY_VERBOSE      = 64;
    const VERBOSITY_VERY_VERBOSE = 128;
    const VERBOSITY_DEBUG        = 256;

    /**
     * @param string|string[] $messages
     * @param bool            $newline
     * @param int             $options A bitmask of options (one of the VERBOSITY constants),
     *                                 0 is considered the same as self::VERBOSITY_NORMAL
     * @return void
     */
    public function write($messages, $newline = false, $options = self::VERBOSITY_NORMAL);

    /**
     * @param string|string[] $messages
     * @param int             $options A bitmask of options (one of the VERBOSITY constants),
     *                                 0 is considered the same as self::VERBOSITY_NORMAL
     * @return void
     */
    public function writeln($messages, $options = self::VERBOSITY_NORMAL);

    /**
     * @param int $level
     * @return void
     */
    public function setVerbosity($level);

    /**
     * @return int
     */
    public function getVerbosity();
}
