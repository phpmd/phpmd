<?php
function testRuleAppliesToFunctionWithEvalExpression()
{
    eval('$a = 42;');
}