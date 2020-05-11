<?php

class testRuleDoesNotAppliesToWhitelistedUnusedLocaleVariable
{
    function testRuleDoesNotAppliesToWhitelistedUnusedLocaleVariable()
    {
        $_ = 42;
    }
}
