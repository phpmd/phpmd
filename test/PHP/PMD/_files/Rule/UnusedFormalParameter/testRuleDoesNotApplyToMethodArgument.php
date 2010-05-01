<?php
class testRuleDoesNotApplyToMethodArgument
{
    public function testRuleDoesNotApplyToMethodArgument($foo)
    {
        $this->bar($foo);
    }
}