<?php

namespace PHPMD\Renderer\Option;

interface Color
{
    /**
     * @param bool $colored
     */
    public function setColored($colored): void;
}
