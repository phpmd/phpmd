<?php
function testRuleAppliesToFunctionWithEmptyCatchBlock()
{
    try {
        // do some stuff
    } catch (Exception $e) {
    }
}
