<?php
class testRuleNotAppliesToMethodWithoutGotoStatementClass
{
    public function testRuleNotAppliesToMethodWithoutGotoStatement($foo)
    {
        return $foo;
    }
}