<?php
class testRuleDoesNotApplyToMethodArgument
{
    public function testRuleDoesNotApplyToMethodArgument()
    {
        $foo = 42;
        $this->bar($foo);
    }
}