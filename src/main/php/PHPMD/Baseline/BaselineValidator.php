<?php

namespace PHPMD\Baseline;

use PHPMD\RuleViolation;

class BaselineValidator
{
    private BaselineMode $baselineMode;

    /** @var BaselineSet */
    private $baselineSet;

    public function __construct(BaselineSet $baselineSet, BaselineMode $baselineMode)
    {
        $this->baselineMode = $baselineMode;
        $this->baselineSet = $baselineSet;
    }

    /**
     * @return bool
     */
    public function isBaselined(RuleViolation $violation)
    {
        $contains = $this->baselineSet->contains(
            $violation->getRule()::class,
            $violation->getFileName(),
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
