<?php
function testRuleNotAppliesToShortVariableNameInCatchStatement()
{
    try {
        foo();
    } catch (Exception $e) {}
}