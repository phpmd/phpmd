<?php
class testRuleDoesNotApplyToMethodWithInheritdocAnnotationCamelCase
{
    /**
     * @inheritDoc
     */
    public function testRuleDoesNotApplyToMethodWithInheritdocAnnotationCamelCase($foo, $bar, $baz)
    {

    }
}
