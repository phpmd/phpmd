<?php

class Foo extends Bar
{
    public function testRuleNotAppliesToParentStaticCall()
    {
        parent::otherCall();
    }
}
