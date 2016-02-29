<?php

class testRuleAppliesMultipleTimesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys
{
    function testRuleAppliesMultipleTimesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys()
    {
        $array = [
            'foo' => 42,
            'foo' => 43,
            'foo' => 44,
            'foo' => 45,
        ];
    }
}
