<?php

function testRuleDoesNotApplyToVariableCapturedByClosureUseClause()
{
    $a = 9;

    return function () use ($a) {
        return $a;
    };
}
