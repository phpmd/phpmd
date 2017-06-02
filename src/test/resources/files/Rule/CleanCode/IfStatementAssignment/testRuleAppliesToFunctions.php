<?php

function testRuleAppliesToFunctions()
{
    if ('foo' || 'bar') { // not applied
        // ...
    }
    if ($foo = 'baz') { // applied
        // ...
    }
}
