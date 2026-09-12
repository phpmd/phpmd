<?php

function testRuleDoesNotApplyToUsedArrowFunctionParameterInNestedFunction()
{
    function z_testRuleDoesNotApplyToUsedArrowFunctionParameterInNestedFunction($arr)
    {
        return array_map(fn (string $value) => $value, $arr);
    }
}
