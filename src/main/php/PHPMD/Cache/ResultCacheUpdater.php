<?php

namespace PHPMD\Cache;

use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Report;
use PHPMD\RuleSet;
use PHPMD\Utility\Paths;

class ResultCacheUpdater
{
    /** @var string */
    private $basePath;

    /**
     * @param string $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @param RuleSet[] $ruleSetList
     * @return ResultCacheState
     */
    public function update(array $ruleSetList, ResultCacheState $state, Report $report)
    {
        // grab a copy of the new violations
        $newViolations = $report->getRuleViolations();

        // add RuleViolations from the result cache to the report
        foreach ($state->getRuleViolations($this->basePath, $ruleSetList) as $ruleViolation) {
            $report->addRuleViolation($ruleViolation);
        }

        // add violations from the report to the result cache
        foreach ($newViolations as $violation) {
            $filePath = Paths::getRelativePath($this->basePath, $violation->getFileName());
            $state->addRuleViolation($filePath, $violation);
        }

        return $state;
    }
}
