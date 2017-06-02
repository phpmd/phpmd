<?php

function testRuleNotAppliesToCatchBlockWithComments()
{
    try {
        // ...
    } catch (Exception $e) {
        // valid line of code
    }
    try {
        // ...
    } catch (Exception $e) {
        /**
         * valid line of code
         */
    }
    try {
        // ...
    } catch (Exception $e) {
        /** valid line of code */
    }
    try {
        // ...
    } catch (Exception $e) {
        /** /** valid line of code */
    }
    try {
        // ...
    } catch (Exception $e) {
        # valid line of code
    }
}
