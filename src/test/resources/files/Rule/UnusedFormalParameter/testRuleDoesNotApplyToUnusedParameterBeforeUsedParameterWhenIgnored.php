<?php
class testRuleDoesNotApplyToUnusedParameterBeforeUsedParameterWhenIgnored
{
    public function testRuleDoesNotApplyToUnusedParameterBeforeUsedParameterWhenIgnored($foo, $bar)
    {
        $bar = 42;
    }
}
