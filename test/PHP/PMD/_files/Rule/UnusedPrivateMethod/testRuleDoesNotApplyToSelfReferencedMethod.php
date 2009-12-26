<?php
class testRuleDoesNotApplyToSelfReferencedMethod
{
    private static function _foo()
    {

    }

    public function bar()
    {
        self::_foo();
    }
}