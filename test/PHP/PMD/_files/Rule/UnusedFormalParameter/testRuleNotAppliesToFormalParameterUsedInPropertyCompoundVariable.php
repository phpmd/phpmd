<?php
class testRuleNotAppliesToFormalParameterUsedInPropertyCompoundVariable
{
    function testRuleNotAppliesToFormalParameterUsedInPropertyCompoundVariable($foo)
    {
        self::${$foo} = 42;
    }
}