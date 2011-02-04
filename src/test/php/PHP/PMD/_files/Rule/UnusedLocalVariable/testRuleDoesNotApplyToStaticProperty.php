<?php
class testRuleDoesNotApplyToStaticProperty
{
    protected static $x = 42;

    public function testRuleDoesNotApplyToStaticProperty()
    {
        return testRuleDoesNotApplyToStaticProperty::$x;
    }
}
