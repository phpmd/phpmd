<?php
class testRuleAppliesToParentReferencedUnusedPrivateMethod extends stdClass
{
    private static function _foo()
    {

    }

    public function  __construct()
    {
        parent::_foo();
    }
}