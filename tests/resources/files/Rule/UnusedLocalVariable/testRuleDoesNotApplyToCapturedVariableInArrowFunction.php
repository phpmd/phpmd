<?php

function testRuleDoesNotApplyToCapturedVariableInArrowFunction()
{
    $a = 9;

    return static fn ($b) => $a;
}
