<?php
class testRuleAppliesToParentReferencedUnusedPrivateMethod extends stdClass
{
    private static function foo()
    {

    }

    public function  __construct()
    {
        parent::foo();
    }
}
