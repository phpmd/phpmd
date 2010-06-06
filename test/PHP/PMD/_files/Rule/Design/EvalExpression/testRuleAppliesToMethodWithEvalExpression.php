<?php
class testRuleAppliesToMethodWithEvalExpression
{
    protected function testRuleAppliesToMethodWithEvalExpression()
    {
        eval('$a = 42;');
    }
}