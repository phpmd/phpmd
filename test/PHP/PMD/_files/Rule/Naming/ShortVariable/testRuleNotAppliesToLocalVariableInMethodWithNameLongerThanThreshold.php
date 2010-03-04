<?php
class testRuleNotAppliesToLocalVariableInMethodWithNameLongerThanThreshold
{
    protected function testRuleNotAppliesToLocalVariableInMethodWithNameLongerThanThreshold()
    {
        $foo = "BAR";
    }
}