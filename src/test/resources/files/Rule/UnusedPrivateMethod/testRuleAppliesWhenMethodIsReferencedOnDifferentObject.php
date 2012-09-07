<?php
class testRuleAppliesWhenMethodIsReferencedOnDifferentObject
{
    private function foo()
    {

    }

    public function bar($object)
    {
        $object->foo();
    }
}
