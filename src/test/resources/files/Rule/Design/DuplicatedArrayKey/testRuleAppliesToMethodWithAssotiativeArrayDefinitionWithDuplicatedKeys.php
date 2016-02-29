<?php

class testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys
{
    function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys()
    {
        return array(
            'foo' => 42,
            'foo' => 43,
        );
    }
}
