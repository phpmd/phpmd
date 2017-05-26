<?php

class testRuleNotAppliesToMethodWithAssotiativeArrayDefinitionWithoutDuplicatedKeys
{
    public function testRuleNotAppliesToMethodWithAssotiativeArrayDefinitionWithoutDuplicatedKeys()
    {
        return array(
            'foo' => 42,
            'bar' => 43,
        );
    }
}
