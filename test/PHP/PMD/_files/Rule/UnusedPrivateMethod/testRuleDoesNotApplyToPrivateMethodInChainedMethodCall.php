<?php
class testRuleDoesNotApplyToPrivateMethodInChainedMethodCall
{
    private function bar()
    {
        return new SplObjectStorage();
    }

    public function add($object)
    {
        return $this->bar()->attach($object);
    }
}