<?php
class testRuleAppliesToUnusedParameterAfterUsedParameterWhenIgnored
{
    public function testRuleAppliesToUnusedParameterAfterUsedParameterWhenIgnored($foo, $bar, $baz)
    {
        $bar = 42;
    }
}