<?php
class testRuleAppliesMultipleTimesToMethodWithEvalExpression
{
    public function testRuleAppliesMultipleTimesToMethodWithEvalExpression()
    {
        if (true) {
            eval('$a = 17;');
        } else if (time() % 42 === 0) {
            eval('$a = 23;');
        }
        eval('$a = 42;');
    }
}