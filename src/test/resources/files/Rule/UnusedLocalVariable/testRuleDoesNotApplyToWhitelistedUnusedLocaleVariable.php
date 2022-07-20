<?php

class testRuleDoesNotApplyToWhitelistedUnusedLocaleVariable
{
    function testRuleDoesNotApplyToWhitelistedUnusedLocaleVariable()
    {
        $_ = 42;
    }
}
