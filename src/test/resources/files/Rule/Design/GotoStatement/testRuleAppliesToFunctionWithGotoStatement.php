<?php
function testRuleAppliesToFunctionWithGotoStatement()
{
    LABEL:
        echo 'FOOBAR';

    if (time() % 23 === 42) {
        goto LABEL;
    }
}