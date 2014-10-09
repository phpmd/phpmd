<?php

class Foo
{
    public function testRuleAppliesToStaticMethodAccessWhenNotAllExcluded()
    {
        Excluded::foo();
        NotExcluded::bar();
    }
}
