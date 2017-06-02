<?php

function testRuleNotAppliesToFunctionWithNonEmptyCatchBlock()
{
    try {
        // ...
    } catch (OutOfBoundsException $e) {
        $e->getLine();
    }
}
