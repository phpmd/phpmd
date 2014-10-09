<?php

class Foo
{
    public function testRuleNotAppliesToStaticMethodAccessWhenExcluded()
    {
        Excluded1::foo();
		Excluded2::bar();
    }
}
