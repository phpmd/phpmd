<?php
class testRuleDoesNotApplyToImplementedInterfaceMethodClass
    implements testRuleDoesNotApplyToImplementedInterfaceMethodInterface
{
    public function testRuleDoesNotApplyToImplementedInterfaceMethod($foo, $bar, $baz)
    {

    }
}

interface testRuleDoesNotApplyToImplementedInterfaceMethodInterface
{
    function testRuleDoesNotApplyToImplementedInterfaceMethod($foo, $bar, $baz);
}
