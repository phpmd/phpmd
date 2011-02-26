<?php
class testRuleAppliesMultipleTimesToMethodWithExitExpression
{
    public function testRuleAppliesMultipleTimesToMethodWithExitExpression()
    {
        if (true) {
            exit(0);
        } else if (time() % 42 === 0) {
            exit(1);
        }
        exit(2);
    }
}