<?php
class testRuleAppliesWhenStaticFieldWithSameNameIsAccessedOnDifferentClass
{
    private $_foo = 23;

    public function  __construct()
    {
        FooBar::$_foo = 23;
    }
}