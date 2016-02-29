<?php

class testRuleNotAppliesToMethodWithAssotiativeArrayDefinitionWithoutDuplicatedKeys
{
    function testRuleNotAppliesToMethodWithAssotiativeArrayDefinitionWithoutDuplicatedKeys()
    {
        $array = [
            'foo' => 42,
            'bar' => 43,
        ];
    }
}
