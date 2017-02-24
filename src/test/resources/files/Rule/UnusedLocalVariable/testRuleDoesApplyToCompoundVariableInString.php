<?php
class testRuleDoesApplyToCompoundVariableInString
{
    public function testRuleDoesApplyToCompoundVariableInString()
    {
        $bar = 'foo';
        return "${bar}_me";
    }
}
