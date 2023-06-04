<?php

namespace PHPMD\Cache;

use PDepend\Input\Filter;
use PHPMD\Report;
use PHPMD\RuleSet;
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

        // Determine if file was modified since last analyse
        if ($this->state === null) {
            $isModified = true;
        } else {
            $isModified = $this->state->isFileModified($filePath, $hash);
        }

        $this->newState->setFileState($filePath, $hash);
        if ($isModified === false) {
            // File was not modified, transfer previous violations
            $this->newState->setViolations($filePath, $this->state->getViolations($filePath));
        }

        return $this->fileIsModified[$filePath] = $isModified;
    }

    /**
     * Stage 2: Invoked when all modified and new files have been inspected and added to the report. Next:
     * - Add new violations from the report to the cache
     * - Add all existing violations from the files that were skipped to the report.
     * @param RuleSet[] $ruleSetList
     * @return ResultCacheState
     */
    public function processReport(array $ruleSetList, Report $report)
    {
        // grab a copy of the new violations
        $newViolations = $report->getRuleViolations();

        // add RuleViolations from the result cache to the report
        foreach ($this->newState->getRuleViolations($this->basePath, $ruleSetList) as $ruleViolation) {
            $report->addRuleViolation($ruleViolation);
        }

        // add violations from the report to the result cache
        foreach ($newViolations as $violation) {
            $filePath = Paths::getRelativePath($this->basePath, $violation->getFileName());
            $this->newState->addRuleViolation($filePath, $violation);
        }

        return $this->newState;
    }
}
