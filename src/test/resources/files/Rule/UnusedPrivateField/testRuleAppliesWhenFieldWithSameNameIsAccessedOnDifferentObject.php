<?php
class testRuleAppliesWhenFieldWithSameNameIsAccessedOnDifferentObject
{
    private $foo = 42;

    public function __construct($object)
    {
        $object->foo = 23;
    }
}
