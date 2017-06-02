<?php

function testRuleAppliesMultipleIfConditions()
{
    if (1 || 0) { // not applied
        // ...
    }
    if (1 == 1 || 1 && 0 and 4 % 2 || ($foo = 1) xor 5 * 4 * 3 * 2 * 1) { // applied
        // ...
    }
}
