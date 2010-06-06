<?php
function testRuleAppliesMultipleTimesToFunctionWithExitExpression()
{
    if (true) {
        exit(0);
    } else if (time() % 42 === 0) {
        exit(1);
    }
    exit(2);
}