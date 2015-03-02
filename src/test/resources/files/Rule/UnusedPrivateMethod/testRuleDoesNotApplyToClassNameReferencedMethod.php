<?php
class testRuleDoesNotApplyToClassNameReferencedMethod
{
    private static function foo()
    {

    }

    public static function bar()
    {
        TestRuleDoesNotApplyToClassNameReferencedMethod::foo();
    }
}
