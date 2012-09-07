<?php
class testRuleAppliesWhenMethodIsReferencedOnDifferentClass
{
    private static function foo()
    {

    }

    public function bar()
    {
        FooBarBaz::foo();
    }
}
