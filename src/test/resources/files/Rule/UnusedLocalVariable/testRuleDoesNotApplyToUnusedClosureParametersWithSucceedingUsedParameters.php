<?php
function testRuleDoesNotApplyToUnusedClosureParametersWithSucceedingUsedParameters()
{
    return function ($a, $b) {
        return $b;
    };
}
