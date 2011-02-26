<?php
class testRuleDoesNotApplyToStaticObjectProperty
{
    protected static $c = null;

    public function testRuleDoesNotApplyToStaticObjectProperty()
    {
        return self::$c->getValue();
    }
}
