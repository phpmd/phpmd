<?php

namespace PHPMD\Cache;

use OutOfBoundsException;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Report;
use PHPMD\RuleSet;
use PHPMD\Utility\Paths;
use Symfony\Component\Console\Output\OutputInterface;

class ResultCacheUpdater
{
    public function __construct(
        private readonly OutputInterface $output,
        private readonly string $basePath,
    ) {
    }

    /**
     * @param list<RuleSet> $ruleSetList
     * @throws OutOfBoundsException
     */
    public function update(array $ruleSetList, ResultCacheState $state, Report $report): ResultCacheState
    {
        // grab a copy of the new violations and processing errors
        $newViolations = $report->getRuleViolations();
        $newErrors = $report->getErrors();

        // add RuleViolations from the result cache to the report
        $violationsFromCache = 0;

        foreach ($state->getRuleViolations($this->basePath, $ruleSetList) as $ruleViolation) {
            $report->addRuleViolation($ruleViolation);
            ++$violationsFromCache;
        }

        // add ProcessingErrors from the result cache to the report
        $errorsFromCache = 0;

        foreach ($state->getProcessingErrors() as $error) {
            $report->addError($error);
            ++$errorsFromCache;
        }

        // add violations from the report to the result cache
        foreach ($newViolations as $violation) {
            $filePath = Paths::getRelativePath($this->basePath, (string) $violation->getFileName());
            $state->addRuleViolation($filePath, $violation);
        }

        // add processing errors from the report to the result cache
        foreach ($newErrors as $error) {
            // errors that cannot be attributed to a file cannot be restored from cache and are not stored
            if ($error->getFile() === '') {
                continue;
            }
            $filePath = Paths::getRelativePath($this->basePath, $error->getFile());
            $state->addError($filePath, $error);
        }

        $this->output->writeln(
            'Cache: added ' . count($newViolations) . ' violations and ' . count($newErrors)
            . ' errors to the result cache.',
            OutputInterface::VERBOSITY_VERY_VERBOSE
        );
        $this->output->writeln(
            'Cache: added ' . $violationsFromCache . ' violations and ' . $errorsFromCache
            . ' errors from the result cache to the report.',
            OutputInterface::VERBOSITY_VERY_VERBOSE
        );

        return $state;
    }
}
