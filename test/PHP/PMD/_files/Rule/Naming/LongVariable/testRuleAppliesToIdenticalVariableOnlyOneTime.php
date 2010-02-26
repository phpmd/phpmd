<?php
class testRuleAppliesToIdenticalVariableOnlyOneTime
{
    function testRuleAppliesToIdenticalVariableOnlyOneTime()
    {
        $thisIsTheFirstReallyLongVariable = 23;
        $thisIsTheSecondReallyLongVariable = 42;
        $thisIsTheFirstReallyLongVariable = 42;
        $thisIsTheSecondReallyLongVariable = 23;
    }
}