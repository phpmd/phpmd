<?php
class testRuleDoesNotApplyToStaticReferencedMethod
{
    private static function foo()
    {

    }

    public static function bar()
    {
        static::foo();
    }
}
