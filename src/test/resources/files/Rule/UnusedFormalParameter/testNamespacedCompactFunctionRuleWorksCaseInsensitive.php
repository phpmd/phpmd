<?php

namespace PHPMDTest;

class testNamespacedCompactFunctionRuleWorksCaseInsensitive
{
    public function testNamespacedCompactFunctionRuleWorksCaseInsensitive($foo, $bar)
    {
        return Compact('foo', 'bar');
    }
}
