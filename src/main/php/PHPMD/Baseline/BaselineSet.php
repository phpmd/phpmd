<?php

namespace PHPMD\Baseline;

class BaselineSet
{
    /** @var ViolationBaseline[] */
    private $violations = array();

    public function addEntry(ViolationBaseline $entry)
    {
        $this->violations[] = $entry;
    }
}
