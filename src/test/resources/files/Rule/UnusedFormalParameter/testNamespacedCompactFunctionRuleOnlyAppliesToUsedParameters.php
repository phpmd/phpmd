<?php

namespace PHPMDTest;

class testNamespacedCompactFunctionRuleOnlyAppliesToUsedParameters
{
    public function testNamespacedCompactFunctionRuleOnlyAppliesToUsedParameters($foo, $bar, $baz)
    {
        return compact('bar');
    }
}
