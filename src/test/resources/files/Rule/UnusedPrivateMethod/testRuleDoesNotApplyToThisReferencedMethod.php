<?php
class testRuleDoesNotApplyToThisReferencedMethod
{
    private function foo()
    {

    }

    protected function bar()
    {
        $this->foo();
    }
}
