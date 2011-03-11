<?php
function testRuleNotAppliesToFunctionWithoutGotoStatement($foo)
{
    return 42 * $foo;
}