<?php

namespace PHPMD\Cache;

use PDepend\Input\Filter;
use PHPMD\Report;
use PHPMD\Utility\Paths;

class ResultCacheEngine implements Filter
{
    /** @var ResultCacheConfig */
    private $config;

    /** @var ResultCacheState|null */
    private $state;

    /** @var ResultCacheState */
    private $newState;

    /** @var string */
    private $basePath;

    /** @var array<string, bool> */
    private $fileIsModified = array();

    /**
     * @param string                $basePath
     * @param ResultCacheConfig     $config
     * @param ResultCacheState|null $state
     */
    public function __construct($basePath, $config, $state)
    {
        $this->basePath = $basePath;
        $this->config   = $config;
        $this->state    = $state;
        $this->newState = new ResultCacheState();
    }

    /**
     * @return ResultCacheConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Stage 1: A hook to allow filtering out certain files from inspection by pdepend.
     * @inheritDoc
     * @return bool `true` will inspect the file, when `false` the file will be filtered out.
     */
    public function accept($relative, $absolute)
    {
        $filePath = Paths::getRelativePath($this->basePath, $absolute);

        // Seemingly Iterator::accept is invoked more than once for the same file. Cache results for performance.
        if (isset($this->fileIsModified[$filePath])) {
            return $this->fileIsModified[$filePath];
        }

        // Determine file hash. Either `timestamp` or `content`
        if ($this->config->getStrategy() === 'timestamp') {
            $hash = (string)filemtime($absolute);
        } else {
            $hash = md5_file($absolute);
        }

        // Determine if file was modified since last analyses
        if ($this->state === null) {
            $isModified = true;
        } else {
            $isModified = $this->state->isFileModified($filePath, $hash);
        }

        if ($isModified === false) {
            // File was not modified, transfer previous violations
            $this->newState->setViolations($filePath, $this->state->getViolations($filePath));
        } else {
            // File was modified, set state
            $this->newState->setFileState($filePath, $hash);
        }

        return $this->fileIsModified[$filePath] = $isModified;
    }

    /**
     * Stage 2: Invoked when all modified and new files have been inspected and added to the report. Next:
     * - Add new violations from the report to the cache
     * - Add all existing violations from the files that were skipped to the report.
     * @return ResultCacheState
     */
    public function processReport(Report $report)
    {
        // grab a copy of the new violations
        $newViolations = $report->getRuleViolations();

        // add violations from the cache to the report
        foreach ($newViolations as $violation) {
            $filePath = Paths::getRelativePath($this->basePath, $violation->getFileName());
            $this->newState->addViolation($filePath, $violation);
        }

        return $this->newState;
    }
}
