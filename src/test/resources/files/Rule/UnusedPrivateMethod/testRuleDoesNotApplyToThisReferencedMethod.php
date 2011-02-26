<?php
class testRuleDoesNotApplyToThisReferencedMethod
{
    private function _foo()
    {

    }

    protected function bar()
    {
        $this->_foo();
    }
}