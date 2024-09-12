<?php

class testRuleDoesNotApplyToFormalParameterUsedAsParameterInStringCompoundVariable
{
    public function testRuleDoesNotApplyToFormalParameterUsedAsParameterInStringCompoundVariable($foo)
    {
        $this->bar("${foo}");
    }

    private function bar($foo)
    {
        return "who $foo?";
    }
}
