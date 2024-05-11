<?php

namespace PHPMD\Baseline;

class BaselineMode
{
    /** Do not generate or update any baseline file */
    public const NONE = 'none';

    /** Generate a baseline file for _all_ current violations */
    public const GENERATE = 'generate';

    /** Remove any non existing violations from the baseline file. Do not baseline any new violations. */
    public const UPDATE = 'update';
}
