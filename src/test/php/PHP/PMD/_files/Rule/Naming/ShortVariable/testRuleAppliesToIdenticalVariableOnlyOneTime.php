<?php
class testRuleAppliesToIdenticalVariableOnlyOneTime
{
    function testRuleAppliesToIdenticalVariableOnlyOneTime()
    {
        $x = 23;
        $y = 42;
        $x = 42;
        $y = 23;
    }
}