<?php

namespace PHPMD\Cache;

use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Console\OutputInterface;
use PHPMD\Report;
use PHPMD\RuleSet;
use PHPMD\Utility\Paths;

class ResultCacheUpdater
{
    /** @var OutputInterface */
    private $output;
    /** @var string */
    private $basePath;

    /**
     * @param string $basePath
     */
    public function __construct(OutputInterface $output, $basePath)
    {
        $this->output   = $output;
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
        $violationsFromCache = 0;

        foreach ($state->getRuleViolations($this->basePath, $ruleSetList) as $ruleViolation) {
            $report->addRuleViolation($ruleViolation);
            ++$violationsFromCache;
        }

        // add violations from the report to the result cache
        foreach ($newViolations as $violation) {
            $filePath = Paths::getRelativePath($this->basePath, $violation->getFileName());
            $state->addRuleViolation($filePath, $violation);
        }

        $this->output->writeln(
            'Cache: added ' . count($newViolations) . ' violations to the result cache.',
            OutputInterface::VERBOSITY_VERY_VERBOSE
        );
        $this->output->writeln(
            'Cache: added ' . $violationsFromCache . ' violations from the result cache to the report.',
            OutputInterface::VERBOSITY_VERY_VERBOSE
        );

        return $state;
    }
}
