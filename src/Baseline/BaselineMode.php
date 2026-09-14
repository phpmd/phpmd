<?php

namespace PHPMD\Baseline;

enum BaselineMode
{
    /**
     * Do not generate or update any baseline file
     */
    case None;

    /**
     * Generate a baseline file for _all_ current violations
     */
    case Generate;

    /**
     * Rewrite the baseline file with only the baselined violations that still exist.
     * Violations that are not in the baseline are reported as usual and are not added to the baseline.
     */
    case Update;
}
