<?php
class testRuleDoesNotApplyToImplementedAbstractMethodClass
    extends testRuleDoesNotApplyToImplementedAbstractMethodParentClass
{
    public function testRuleDoesNotApplyToImplementedAbstractMethod($foo, $bar, $baz)
    {

    }
}

abstract class testRuleDoesNotApplyToImplementedAbstractMethodParentClass
{
    public abstract function testRuleDoesNotApplyToImplementedAbstractMethod($foo, $bar, $baz);
}
