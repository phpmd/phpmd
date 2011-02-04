<?php
class testRuleAppliesWhenFieldWithSameNameIsAccessedOnDifferentObject
{
    private $_foo = 42;

    public function __construct($object)
    {
        $object->_foo = 23;
    }
}