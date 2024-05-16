<?php

namespace PHPMD\Renderer\Option;

interface Verbose
{
    /**
     * @param int $level
     */
    public function setVerbosityLevel($level): void;
}
