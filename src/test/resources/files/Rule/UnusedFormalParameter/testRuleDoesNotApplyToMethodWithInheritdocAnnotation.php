<?php
class testRuleDoesNotApplyToMethodWithInheritdocAnnotation
{
    /**
     * @inheritdoc
     */
    public function testRuleDoesNotApplyToMethodWithInheritdocAnnotation($foo, $bar, $baz)
    {

    }
}
