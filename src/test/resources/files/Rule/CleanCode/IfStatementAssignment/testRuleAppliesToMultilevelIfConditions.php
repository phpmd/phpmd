<?php

function testRuleAppliesToMultilevelIfConditions()
{
    if (1 || 0) {
        if (1 == 1 || $foo = 'baz') {
            // applied
        } elseif (1 != [] && 'foo' != 'baz' && $bar = 'baz') {
            // applied
        } elseif (1 % 2 !== !false && $baz = 1 + 1 + 1 - 3) {
            // applied
            if ($foo == 'baz') {
                // not applied
                if (3 - 2 == 3) {
                    // not applied
                    if (true) {
                        // not applied
                        if (1) {
                            // applied
                        } elseif ($foo = 1) {
                            // applied
                        } elseif ($foo = 2) {
                            // applied
                        } elseif (5 % 5 == 0) {
                            // not applied
                        }
                    }
                }
            }
        }
    }
    if (1 == 1 || 1 && 0 and 4 % 2 || ($foo = 1) xor 5 * 4 * 3 * 2 * 1) {
        // applied
    }
}
