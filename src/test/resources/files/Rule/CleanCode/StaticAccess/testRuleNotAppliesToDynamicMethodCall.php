<?php

class Foo
{
    public function testRuleNotAppliesToDynamicMethodCall()
    {
        $foo = new Foo();
        $foo->bar();
    }
}
