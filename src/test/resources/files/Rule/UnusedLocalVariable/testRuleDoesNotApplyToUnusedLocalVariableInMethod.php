<?php
class testRuleDoesNotApplyToUnusedLocalVariableInMethod
{
    function testRuleDoesNotApplyToUnusedLocalVariableInMethod()
    {
        static $foo;

        return $foo++;
    }
}