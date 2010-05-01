<?php
class testRuleDoesNotApplyToLocalVariableUsedInCompoundVariable
{
    protected static $foo = null;

    public function testRuleDoesNotApplyToLocalVariableUsedInCompoundVariable()
    {
        $bar = 'foo';
        return self::${$bar};
    }
}