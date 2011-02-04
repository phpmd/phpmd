<?php
class testRuleDoesNotApplyToSelfAccessedPrivateField
{
    private static $_foo = 42;

    public function  __construct()
    {
        self::$_foo = 23;
    }
}