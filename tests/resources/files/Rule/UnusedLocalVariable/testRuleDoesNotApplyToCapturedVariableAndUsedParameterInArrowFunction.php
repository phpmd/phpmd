<?php

function testRuleDoesNotApplyToCapturedVariableAndUsedParameterInArrowFunction()
{
    $a = 9;

    return static fn ($b) => $a + $b;
}
