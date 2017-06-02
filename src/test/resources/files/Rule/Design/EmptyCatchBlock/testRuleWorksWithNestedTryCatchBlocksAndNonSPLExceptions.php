<?php

function testRuleWorksWithNestedTryCatchBlocksAndNonSPLExceptions()
{
    try {
        // ...
    } catch (OutOfRangeException $e) {
        try {
            // ...
        } catch (PHPUnit_Framework_Exception $e) {
            try {
                // ...
            } catch (Exception $e) {
            }
        }
    }
}
