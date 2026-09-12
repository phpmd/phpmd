<?php

function testRuleDoesNotApplyToCapturedVariableInNestedArrowFunction()
{
    $a = 9;

    return static fn ($b) => static fn ($c) => $a + $b + $c;
}
