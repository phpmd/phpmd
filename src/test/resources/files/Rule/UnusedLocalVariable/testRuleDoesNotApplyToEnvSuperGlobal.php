<?php
class testRuleDoesNotApplyToEnvSuperGlobal
{
    function testRuleDoesNotApplyToEnvSuperGlobal()
    {
        return $_ENV;
    }
}