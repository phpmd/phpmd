<?php
function testRuleNotAppliesToLocalVariableInFunctionWithNameSmallerThanThreshold()
{
    $fooBar = 42;
}