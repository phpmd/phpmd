<?php
class testRuleDoesNotApplyToStaticAccessedPrivateField
{
    private static $_foo = 23;

    public function  __construct()
    {
        static::$_foo = 42;
    }
}