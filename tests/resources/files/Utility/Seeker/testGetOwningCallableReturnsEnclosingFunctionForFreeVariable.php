<?php

function testGetOwningCallableReturnsEnclosingFunctionForFreeVariable()
{
    $a = 9;

    return static fn ($b) => $a;
}
