<?php
class testRuleNotAppliesToFormalParameterUsedInMethodCompoundVariable
{
    public function testRuleNotAppliesToFormalParameterUsedInMethodCompoundVariable($foo)
    {
        self::${$foo}();
    }
}