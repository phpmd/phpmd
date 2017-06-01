<?php

namespace PHPMDTest;

/**
 * @see https://github.com/phpmd/phpmd/issues/110
 */
class testRuleDoesNotApplyToPrivateMethodInChainedMethodCallInNumberBiggerThanTwo
{

    public function foo()
    {
        $this
            ->bar()
            ->baz()
            ->baw();
    }

    public function abc()
    {
        $this
            ->bar()
            ->baz();
        $this->baw();
    }

    public function xyz()
    {
        $this
            ->bar()
            ->baz()
            ->baw()
            ->bar()
            ->baz()
            ->baw()
            ->bar()
            ->baz()
            ->baw();
    }

    /**
     * @return $this
     */
    private function bar()
    {
        // Do some stuff ...
        return $this;
    }

    /**
     * @return $this
     */
    private function baz()
    {
        // Do some stuff ...
        return $this;
    }

    /**
     * @return $this
     */
    private function baw()
    {
        // Do some stuff ...
        return $this;
    }
}
