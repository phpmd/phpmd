<?php

class Foo
{
    public function testRuleNotAppliesToConstantAccess()
    {
        Foo::BAR;
    }
}
