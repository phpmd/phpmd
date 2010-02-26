<?php
function testRuleNotAppliesToLocalVariableInFunctionWithNameEqualToThreshold()
{
    $foo = 42;
}