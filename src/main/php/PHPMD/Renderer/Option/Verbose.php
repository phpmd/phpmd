<?php

namespace PHPMD\Renderer\Option;

interface Verbose
{
    /**
     * @param int $level
     *
     * @return void
     */
    public function setVerbosityLevel($level);
}
