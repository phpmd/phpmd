<?php

function testRuleAppliesMultipleTimesInOneIfCondition()
{
    if (1 || 0) { // not applied
        // ...
    }
    if ($foo = 'bar' && $bar = 'baz' || $baz = 'foo') { // applied 3 times
        // ...
    }
}
