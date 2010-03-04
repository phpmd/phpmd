<?php
class testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes
{
    function testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimes()
    {
        $x = 42;
    }

    function testRuleAppliesToIdenticalVariablesInDifferentContextsSeveralTimesNext()
    {
        $x = 23;
    }
}