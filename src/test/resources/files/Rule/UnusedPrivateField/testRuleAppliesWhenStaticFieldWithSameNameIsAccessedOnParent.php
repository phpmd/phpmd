<?php
class testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnParent extends stdClass
{
    private $foo = 23;

    public function __construct()
    {
        parent::$foo = 42;
    }
}
