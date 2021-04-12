<?php

namespace PHPMD\Baseline;

use PHPMD\RuleViolation;

class BaselineValidator
{
    /** @var string */
    private $baselineMode;

    /** @var BaselineSet */
    private $baselineSet;

    /**
     * @param string $baselineMode
     */
    public function __construct(BaselineSet $baselineSet, $baselineMode)
    {
        $this->baselineMode = $baselineMode;
        $this->baselineSet  = $baselineSet;
    }

    /**
     * @return bool
     */
    public function isBaselined(RuleViolation $violation)
    {
        $contains = $this->baselineSet->contains(
            get_class($violation->getRule()),
            $violation->getFileName(),
            $violation->getMethodName()
        );

        // regular baseline: violations is baselined if it is in the BaselineSet
        if ($this->baselineMode === BaselineMode::NONE) {
            return $contains;
        }

        // update baseline: violation _can_ be baselined if it was already in the BaselineSet
        if ($this->baselineMode === BaselineMode::UPDATE) {
            return $contains === false;
        }

        return false;
    }
}
