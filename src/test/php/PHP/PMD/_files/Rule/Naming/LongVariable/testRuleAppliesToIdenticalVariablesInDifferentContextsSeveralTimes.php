<?php
class testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes
{
    function testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes()
    {
        $reallyLongLocalVariableName = 23;
    }

    function testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimesTwo()
    {
        $reallyLongLocalVariableName = 42;
    }
}