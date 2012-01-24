<?php
class testRuleDoesNotApplyToInheritMethodClass
    extends testRuleDoesNotApplyToInheritMethodParentClass
{
    public function testRuleDoesNotApplyToInheritMethod($foo, $bar)
    {

    }
}

class testRuleDoesNotApplyToInheritMethodParentClass
{
    public function testRuleDoesNotApplyToInheritMethod($foo, $bar)
    {

    }
}
