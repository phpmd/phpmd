<?php

function testGetOwningCallableReturnsClosureWhenParameterShadowsOuterVariable()
{
    $b = 100;

    return static fn ($b) => $b;
}
