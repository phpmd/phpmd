<?php
class testRuleNotAppliesToLocalVariableInMethodWithNameShorterThanThreshold
{
    function testRuleNotAppliesToLocalVariableInMethodWithNameShorterThanThreshold()
    {
        $fooBar = 42;
    }
}