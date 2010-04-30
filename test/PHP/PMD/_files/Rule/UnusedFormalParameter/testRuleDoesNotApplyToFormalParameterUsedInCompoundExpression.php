<?php
class testRuleDoesNotApplyToFormalParameterUsedInCompoundExpression
{
    public static $foo = null;

    public function testRuleDoesNotApplyToFormalParameterUsedInCompoundExpression($bar)
    {
        self::${$bar} = 42;
    }
}