<?php

function testRuleNotAppliesToFunctionWithNonEmptyCatchBlock()
{
    try {
        // let's do some stuff
    } catch (Exception $e) {
        log_exception($e);
    }
}
