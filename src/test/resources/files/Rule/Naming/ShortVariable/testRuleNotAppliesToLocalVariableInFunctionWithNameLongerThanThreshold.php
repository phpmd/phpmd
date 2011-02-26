<?php
function testRuleNotAppliesToLocalVariableInFunctionWithNameLongerThanThreshold()
{
    $foo = "BAR";
}