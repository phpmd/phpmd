<?php

namespace PHPMDTest;

class testNamespacedCompactFunctionRuleDoesNotApply
{
    public function testNamespacedCompactFunctionRuleDoesNotApply($foo, $bar)
    {
        return compact('foo', 'bar');
    }
}
