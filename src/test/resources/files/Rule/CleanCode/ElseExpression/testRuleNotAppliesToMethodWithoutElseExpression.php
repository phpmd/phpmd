<?php

class Foo
{
    function testRuleNotAppliesToMethodWithoutElseExpression()
    {
        if (true) {
        } else if (true) {
        }
    }
}
