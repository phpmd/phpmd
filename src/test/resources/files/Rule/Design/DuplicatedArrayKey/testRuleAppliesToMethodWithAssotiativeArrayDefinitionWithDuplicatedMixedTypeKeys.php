<?php

class testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys
{
    function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys()
    {
        $array = [
            123 => 42,
            '123' => 43,
        ];
    }
}
