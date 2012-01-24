<?php
function testRuleDoesNotApplyToFunctionParameterNamedArgv($argv)
{
    foreach ($argv as $arg)
    {
        echo $arg;
    }
}
