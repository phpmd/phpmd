<?php

class testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys
{
    function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys()
    {
        $array = [
            'foo' => 42,
            'foo' => 43,
        ];
    }
}
