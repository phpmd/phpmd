<?php

class testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys
{
    function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys()
    {
        return array(
            123 => 42,
            '123' => 43,
        );
    }
}
