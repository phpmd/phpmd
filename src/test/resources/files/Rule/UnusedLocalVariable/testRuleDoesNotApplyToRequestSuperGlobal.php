<?php
class testRuleDoesNotApplyToRequestSuperGlobal
{
    function testRuleDoesNotApplyToRequestSuperGlobal()
    {
        return $_REQUEST;
    }
}