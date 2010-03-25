<?php
class testRuleDoesNotApplyToArgcSuperGlobal
{
    function testRuleDoesNotApplyToArgcSuperGlobal()
    {
        return $argc;
    }
}