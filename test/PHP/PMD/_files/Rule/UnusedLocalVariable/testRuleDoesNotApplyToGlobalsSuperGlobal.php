<?php
class testRuleDoesNotApplyToGlobalsSuperGlobal
{
    function testRuleDoesNotApplyToGlobalsSuperGlobal()
    {
        return $GLOBALS;
    }
}