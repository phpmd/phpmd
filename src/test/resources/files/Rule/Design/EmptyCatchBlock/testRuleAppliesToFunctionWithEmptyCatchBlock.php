<?php

function testRuleAppliesToFunctionWithEmptyCatchBlock()
{
    try {
        // ...
    } catch (Exception $e) {
    }
    try {
        // ...
    } catch (OutOfRangeException $e) {
    }
    try {
        // ...
    } catch (\ErrorException $e) {
    }
}
