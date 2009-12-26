<?php
class testRuleAppliesWhenPropertyWithSimilarNameIsReferenced
{
    private function _foo()
    {

    }

    public function bar()
    {
        $this->_foo = 42;
    }
}