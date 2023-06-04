<?php

namespace PHPMD\Cache;

use PHPMD\Report;
use PHPMD\RuleSet;
use PHPMD\Utility\Paths;

class ResultCacheEngine
{
    /** @var string */
    private $basePath;

    /** @var ResultCacheConfig */
    private $config;

    /** @var ResultCacheFileFilter */
    private $fileFilter;

    /**
     * @param string                $basePath
     * @param ResultCacheConfig     $config
     * @param ResultCacheFileFilter $fileFilter
     */
    public function __construct($basePath, $config, $fileFilter)
    {
        $this->basePath   = $basePath;
        $this->config     = $config;
        $this->fileFilter = $fileFilter;
    }

    /**
     * @return ResultCacheConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return ResultCacheFileFilter
     */
    public function getFileFilter()
    {
        return $this->fileFilter;
    }

    /**
     * Invoked when all modified and new files have been inspected and added to the report. Next:
     * - Add new violations from the report to the cache
     * - Add all existing violations from the files that were skipped to the report.
     * @param RuleSet[] $ruleSetList
     * @return ResultCacheState
     */
    public function processReport(array $ruleSetList, Report $report)
    {
        $newState = $this->fileFilter->getNewState();

        // grab a copy of the new violations
        $newViolations = $report->getRuleViolations();

        // add RuleViolations from the result cache to the report
        foreach ($newState->getRuleViolations($this->basePath, $ruleSetList) as $ruleViolation) {
            $report->addRuleViolation($ruleViolation);
        }

        // add violations from the report to the result cache
        foreach ($newViolations as $violation) {
            $filePath = Paths::getRelativePath($this->basePath, $violation->getFileName());
            $newState->addRuleViolation($filePath, $violation);
        }

        return $newState;
    }
}
