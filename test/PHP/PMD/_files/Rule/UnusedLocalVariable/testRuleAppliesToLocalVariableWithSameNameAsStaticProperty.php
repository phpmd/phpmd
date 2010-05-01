<?php
class testRuleAppliesToLocalVariableWithSameNameAsStaticProperty
{
    protected $foo = 42;
    function testRuleAppliesToLocalVariableWithSameNameAsStaticProperty()
    {
        $foo = 23;
        echo self::$foo;
    }
}