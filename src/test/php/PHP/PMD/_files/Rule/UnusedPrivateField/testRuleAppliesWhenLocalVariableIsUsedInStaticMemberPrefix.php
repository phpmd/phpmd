<?php
class testRuleAppliesWhenLocalVariableIsUsedInStaticMemberPrefix
{
    private static $_foo = 23;
    public static $foo = 17;

    public function bar()
    {
        self::${$_foo = 'foo'} = 42;
    }
}

$o = new testRuleAppliesWhenLocalVariableIsUsedInStaticMemberPrefix();
$o->bar();
var_dump($o::$foo);