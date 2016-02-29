<?php

class testRuleNotAppliesToMethodWithAssotiativeArrayDefinitionWithoutDuplicatedKeys
{
    function testRuleNotAppliesToMethodWithAssotiativeArrayDefinitionWithoutDuplicatedKeys()
    {
        return array(
            'foo' => 42,
            'bar' => 43,
        );
    }
}
