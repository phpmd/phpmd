<?php
class testRuleNotDoesNotApplyToMethodWithFuncGetArgs
{
    public function testRuleDoesNotApplyToMethodWithFuncGetArgs($foo, $bar)
    {
        print_r(func_get_args());
    }
}