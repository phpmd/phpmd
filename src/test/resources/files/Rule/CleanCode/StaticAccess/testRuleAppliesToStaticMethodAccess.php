<?php

class Foo
{
    public function testRuleAppliesToStaticMethodAccess()
    {
        Foo::create();
    }
}
