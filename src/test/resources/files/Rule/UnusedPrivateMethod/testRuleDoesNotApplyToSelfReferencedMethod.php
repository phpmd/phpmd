<?php
class testRuleDoesNotApplyToSelfReferencedMethod
{
    private static function foo()
    {

    }

    public function bar()
    {
        self::foo();
    }
}
