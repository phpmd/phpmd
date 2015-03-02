<?php
class testRuleDoesNotApplyToGetSuperGlobal
{
    function testRuleDoesNotApplyToGetSuperGlobal()
    {
        return $_GET;
    }
}