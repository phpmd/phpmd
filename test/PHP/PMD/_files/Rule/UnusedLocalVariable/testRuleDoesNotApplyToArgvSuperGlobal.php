<?php
class testRuleDoesNotApplyToArgvSuperGlobal
{
    function testRuleDoesNotApplyToArgvSuperGlobal()
    {
        return $argv;
    }
}