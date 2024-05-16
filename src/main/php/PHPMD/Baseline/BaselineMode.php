<?php

namespace PHPMD\Baseline;

enum BaselineMode
{
    /** Do not generate or update any baseline file */
    case None;

    /** Generate a baseline file for _all_ current violations */
    case Generate;

    /** Remove any non existing violations from the baseline file. Do not baseline any new violations. */
    case Update;
}
