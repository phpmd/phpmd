<?php

namespace PHPMD\Baseline;

use PHPMD\RuleViolation;

class BaselineValidator
{
    public function __construct(
        private readonly BaselineSet $baselineSet,
    ) {
    }

    /**
     * A violation is baselined when a matching entry exists in the baseline set.
     */
    public function isBaselined(RuleViolation $violation): bool
    {
        return $this->baselineSet->contains(
            $violation->getRule()::class,
            (string) $violation->getFileName(),
            $violation->getMethodName()
        );
    }
}
