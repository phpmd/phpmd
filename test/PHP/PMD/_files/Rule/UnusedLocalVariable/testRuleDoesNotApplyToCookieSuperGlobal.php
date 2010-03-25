<?php
class testRuleDoesNotApplyToCookieSuperGlobal
{
    function testRuleDoesNotApplyToCookieSuperGlobal()
    {
        return $_COOKIE;
    }
}