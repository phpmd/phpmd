<?php
class testRuleAppliesWhenMethodIsReferencedOnDifferentObject
{
    private function _foo()
    {

    }

    public function bar($object)
    {
        $object->_foo();
    }
}