<?php
class testRuleDoesNotApplyToStaticArrayProperty
{
    protected static $a = array();

    public function testRuleDoesNotApplyToStaticArrayProperty()
    {
        return self::$a[0];
    }
}
