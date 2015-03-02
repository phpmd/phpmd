<?php
class testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnDifferentClass
{
    private $foo = 23;

    public function  __construct()
    {
        FooBar::$foo = 23;
    }
}
