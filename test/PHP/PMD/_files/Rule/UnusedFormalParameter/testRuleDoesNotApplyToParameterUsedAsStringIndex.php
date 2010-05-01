<?php
class testRuleDoesNotApplyToParameterUsedAsStringIndex
{
    public function testRuleDoesNotApplyToParameterUsedAsStringIndex($foo)
    {
        self::$bar{$foo};
    }
}