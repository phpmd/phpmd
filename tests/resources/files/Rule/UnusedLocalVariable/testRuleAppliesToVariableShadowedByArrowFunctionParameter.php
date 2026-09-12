<?php

function testRuleAppliesToVariableShadowedByArrowFunctionParameter()
{
    $b = 100;

    return static fn ($b) => $b;
}
