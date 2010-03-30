<?php
function testRuleDoesNotApplyToUnusedLocalVariableInFunction()
{
    static $bar;

    $bar = 42;
}