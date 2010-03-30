<?php
class testRuleDoesNotApplyToAnyStaticLocalVariable
{
    public function testRuleDoesNotApplyToAnyStaticLocalVariable()
    {
        static $foo = 42, $bar = 23;
        static $baz = T_FOO_BAR;

        echo $foo, $bar, $baz;
    }
}