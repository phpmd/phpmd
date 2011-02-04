<?php
class testRuleDoesNotApplyToSessionSuperGlobal
{
    function testRuleDoesNotApplyToSessionSuperGlobal()
    {
        return $_SESSION;
    }
}