<?php

namespace test\resources\files\Rule\CleanCode\StaticAccess;

class Foo
{
    static public function testRuleNotAppliesToSelfStaticCall()
    {
        self::bar();
    }
}
