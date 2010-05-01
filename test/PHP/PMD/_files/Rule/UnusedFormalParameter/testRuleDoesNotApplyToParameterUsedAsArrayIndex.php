<?php
class testRuleDoesNotApplyToParameterUsedAsArrayIndex
{
    public function testRuleDoesNotApplyToParameterUsedAsArrayIndex($foo)
    {
        self::$bar[$foo];
    }
}