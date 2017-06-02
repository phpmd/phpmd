<?php

function testRuleWorksWithNestedTryCatchBlocks()
{
    try {
        // ..
    } catch (Exception $e) {
        try {
            // ..
        } catch (Exception $e) {
            try {
                // ..
            } catch (Exception $e) {
                $e->getLine();
            }
        }
    }
}
