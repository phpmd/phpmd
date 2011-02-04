<?php
class testRuleAppliesWhenMethodIsReferencedOnDifferentClass
{
    private static function _foo()
    {

    }

    public function bar()
    {
        FooBarBaz::_foo();
    }
}