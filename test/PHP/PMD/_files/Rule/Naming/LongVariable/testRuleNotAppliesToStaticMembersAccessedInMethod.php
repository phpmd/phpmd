<?php
class testRuleNotAppliesToStaticMembersAccessedInMethodClass
{
    private static $testRuleNotAppliesToStaticMembersAccessedInMethod;

    public static function testRuleNotAppliesToStaticMembersAccessedInMethod()
    {
        self::$testRuleNotAppliesToStaticMembersAccessedInMethod = 42;
    }
}
