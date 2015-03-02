<?php
class testRuleDoesNotApplyToMethodParameterNamedArgvClass
{
    public function testRuleDoesNotApplyToMethodParameterNamedArgv($argv)
    {
        foreach ($argv as $arg)
        {
            echo $arg;
        }
    }
}
