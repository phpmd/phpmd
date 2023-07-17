<?php

class testRuleDoesNotApplyToFormalParameterUsedInStringCompoundVariable
{
    public function testRuleDoesNotApplyToFormalParameterUsedInStringCompoundVariable($foo)
    {
        return "me_${foo}";
    }
}
