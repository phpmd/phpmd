<?php
class testCompactFunctionRuleOnlyAppliesToUsedParameters
{
    public function testCompactFunctionRuleOnlyAppliesToUsedParameters($foo, $bar, $baz)
    {
        return compact('bar');
    }
}
