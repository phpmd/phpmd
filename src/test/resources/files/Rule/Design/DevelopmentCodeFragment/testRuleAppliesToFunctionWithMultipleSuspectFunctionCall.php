<?php
function testRuleAppliesToFunctionWithMultipleSuspectFunctionCall()
{
    var_dump(__FUNCTION__);
    debug_print_backtrace();
    debug_zval_dump($GLOBALS);
}
