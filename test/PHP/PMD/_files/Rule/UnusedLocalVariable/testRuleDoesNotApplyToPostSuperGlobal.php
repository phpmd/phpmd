<?php
class testRuleDoesNotApplyToPostSuperGlobal
{
    function testRuleDoesNotApplyToPostSuperGlobal()
    {
        return $_POST;
    }
}