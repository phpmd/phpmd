<?php

namespace PHPMDTest;

class testNamespacedCompactFunctionRuleWorksCaseInsensitive
{
    public function testNamespacedCompactFunctionRuleWorksCaseInsensitive()
    {
        $foo = 1; $bar = 2; $baz = 0;

        return Compact('foo', 'bar', 'baz');
    }
}
