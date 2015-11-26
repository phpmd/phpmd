<?php
class testRuleDoesNotApplyToMultipleUnusedParametersBeforeUsedParameterWhenIgnored
{
    public function testRuleDoesNotApplyToMultipleUnusedParametersBeforeUsedParameterWhenIgnored($foo, $bar, $baz, $qux)
    {
        $bar = 42;
        $qux = "So long, and thanks for all the fish!";
    }
}
