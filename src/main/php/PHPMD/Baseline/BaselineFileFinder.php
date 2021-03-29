<?php

namespace PHPMD\Baseline;

use PHPMD\TextUI\CommandLineOptions;
use RuntimeException;

class BaselineFileFinder
{
    const DEFAULT_FILENAME = 'phpmd.baseline.xml';

    /** @var CommandLineOptions */
    private $options;

    public function __construct(CommandLineOptions $options)
    {
        $this->options = $options;
    }

    /**
     * Try to find the violation baseline file
     *
     * @param bool $shouldExist if true, the baseline filepath should point to an existing file
     * @return string|null
     * @throws RuntimeException
     */
    public function find($shouldExist)
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

        $baselinePath = dirname($rulePath) . '/' . self::DEFAULT_FILENAME;
        if ($shouldExist === true && file_exists($baselinePath) === false) {
            return null;
        }

        return $baselinePath;
    }
}
