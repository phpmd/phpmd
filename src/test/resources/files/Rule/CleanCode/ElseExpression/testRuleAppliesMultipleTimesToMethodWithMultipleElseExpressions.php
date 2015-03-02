<?php

class Foo
{
    public function testRuleAppliesMultipleTimesToMethodWithMultipleElseExpressions()
    {
        if (true) {
        } else {
        }
        if (true) {
        } else {
        }
        if (true) {
        } else {
        }
    }
}
