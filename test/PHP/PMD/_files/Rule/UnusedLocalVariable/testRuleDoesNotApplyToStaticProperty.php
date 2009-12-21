<?php
class testRuleDoesNotApplyToStaticProperty
{
    protected static $x = 42;

    public static function testRuleDoesNotApplyToStaticProperty()
    {
        return testRuleDoesNotApplyToStaticProperty::$x;
    }
}