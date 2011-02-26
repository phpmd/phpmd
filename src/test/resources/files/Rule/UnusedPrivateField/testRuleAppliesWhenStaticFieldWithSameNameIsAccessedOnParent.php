<?php
class testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnParent extends stdClass
{
    private $_foo = 23;

    public function __construct()
    {
        parent::$_foo = 42;
    }
}