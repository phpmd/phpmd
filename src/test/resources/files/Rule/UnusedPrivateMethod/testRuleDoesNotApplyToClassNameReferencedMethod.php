<?php
class testRuleDoesNotApplyToClassNameReferencedMethod
{
    private static function _foo()
    {

    }

    public static function bar()
    {
        TestRuleDoesNotApplyToClassNameReferencedMethod::_foo();
    }
}