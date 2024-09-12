<?php

class ClassWithMultipleViolations
{
    public function method($a)
    {
        return $a;
    }

    /**
     * @return bool
     */
    public function getValue()
    {
        return true;
    }
}
