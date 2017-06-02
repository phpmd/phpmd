<?php

function testRuleAppliesToNonStandardExceptions()
{
    try {
        // ..
    } catch (PHPUnit_Framework_Exception $e) {
        try {
            // ..
        } catch (OutOfBoundsException $e) {
            try {
                // ..
            } catch (InvalidArgumentException $e) {
            }
        }
    }
}
