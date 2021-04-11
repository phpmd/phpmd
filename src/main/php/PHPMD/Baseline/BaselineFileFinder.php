<?php

namespace PHPMD\Baseline;

use PHPMD\TextUI\CommandLineOptions;
use RuntimeException;

class BaselineFileFinder
{
    const DEFAULT_FILENAME = 'phpmd.baseline.xml';

    /** @var CommandLineOptions */
    private $options;

    /** @var bool */
    private $existingFile = false;

    /** @var bool */
    private $notNull = false;

    public function __construct(CommandLineOptions $options)
    {
        $this->options = $options;
    }

    /**
     * The baseline filepath should point to an existing file (or null)
     * @return $this
     */
    public function existingFile()
    {
        $this->existingFile = true;
        return $this;
    }

    /**
     * if true, the finder `must` find a file path, but doesn't necessarily exist
     * @return $this
     */
    public function notNull()
    {
        $this->notNull = true;
        return $this;
    }

    /**
     * Find the violation baseline file
     *
     * @return string|null
     * @throws RuntimeException
     */
    public function find()
    {
        $file = $this->tryFind();
        if ($file === null && $this->notNull === true) {
            throw new RuntimeException('Unable to find the baseline file. Use --baseline-file to specify the filepath');
        }

        return $file;
    }

    /**
     * Try to find the violation baseline file
     *
     * @return string|null
     * @throws RuntimeException
     */
    private function tryFind()
    {
        // read baseline file from cli arguments
        $file = $this->options->baselineFile();
        if ($file !== null) {
            return $file;
        }

        // find baseline file next to the (first) ruleset
        $ruleSets = explode(',', $this->options->getRuleSets());
        $rulePath = realpath($ruleSets[0]);
        if ($rulePath === false) {
            return null;
        }

        // create file path and check for existence
        $baselinePath = dirname($rulePath) . '/' . self::DEFAULT_FILENAME;
        if ($this->existingFile === true && file_exists($baselinePath) === false) {
            return null;
        }

        return $baselinePath;
    }
}
