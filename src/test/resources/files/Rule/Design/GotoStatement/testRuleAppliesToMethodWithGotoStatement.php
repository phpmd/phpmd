<?php
class testRuleAppliesToMethodWithGotoStatementClass
{
    public function testRuleAppliesToMethodWithGotoStatement()
    {
        LABEL:
            echo 'YES';

        if (time() % 42 === 0) {
            goto LABEL;
        }
    }
}