<?php
class testRuleDoesNotApplyToServerSuperGlobal
{
    function testRuleDoesNotApplyToServerSuperGlobal()
    {
        return $_SERVER;
    }
}