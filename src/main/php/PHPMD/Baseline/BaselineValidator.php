<?php

namespace PHPMD\Baseline;

use PHPMD\RuleViolation;

class BaselineValidator
{
    public function __construct(
        private readonly BaselineSet $baselineSet,
        private readonly BaselineMode $baselineMode,
    ) {
    }

    public function isBaselined(RuleViolation $violation): bool
    {
        $contains = $this->baselineSet->contains(
            $violation->getRule()::class,
            (string) $violation->getFileName(),
            $violation->getMethodName()
        );

        // regular baseline: violations is baselined if it is in the BaselineSet
        if ($this->baselineMode === BaselineMode::None) {
            return $contains;
        }

        // update baseline: violation _can_ be baselined if it was already in the BaselineSet
        if ($this->baselineMode === BaselineMode::Update) {
            return !$contains;
        }

        return false;
    }
}
