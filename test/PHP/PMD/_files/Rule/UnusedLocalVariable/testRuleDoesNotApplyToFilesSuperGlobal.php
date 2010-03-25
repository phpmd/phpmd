<?php
class testRuleDoesNotApplyToFilesSuperGlobal
{
    function testRuleDoesNotApplyToFilesSuperGlobal()
    {
        return $_FILES;
    }
}