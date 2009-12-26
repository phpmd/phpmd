<?php
class testRuleDoesNotApplyToStaticReferencedMethod
{
    private static function _foo()
    {

    }

    public static function bar()
    {
        static::_foo();
    }
}