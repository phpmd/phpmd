<?php

class testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys
{
    public function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys()
    {
        return array(
            'foo' => 42,
            'foo' => 43,
        );
    }
}
