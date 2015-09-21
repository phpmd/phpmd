<?php
class class_testRuleAppliesToMethodWithMultipleSuspectFunctionCall
{
    public function testRuleAppliesToMethodWithMultipleSuspectFunctionCall()
    {
        var_dump(__FUNCTION__);
        print_r(__METHOD__);
        debug_zval_dump($this);
    }
}
