<?php
class testRuleAppliesToLocalVariableWithSameNameAsStaticArrayProperty
{
    protected $foo = array(array(1=>42));

    public function testRuleAppliesToLocalVariableWithSameNameAsStaticArrayProperty()
    {
        $foo = 23;
        return self::$foo[0][1];
    }
}