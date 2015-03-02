<?php
class testRuleAppliesWhenPropertyWithSimilarNameIsReferenced
{
    private function foo()
    {

    }

    public function bar()
    {
        $this->foo = 42;
    }
}
