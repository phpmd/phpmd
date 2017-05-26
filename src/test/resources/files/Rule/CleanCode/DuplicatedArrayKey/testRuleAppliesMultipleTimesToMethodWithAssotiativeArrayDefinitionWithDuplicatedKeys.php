<?php

class testRuleAppliesMultipleTimesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys
{
    public function testRuleAppliesMultipleTimesToMethodWithAssotiativeArrayDefinitionWithDuplicatedKeys()
    {
        return array(
            'foo' => 42,
            'foo' => 43,
            'foo' => 44,
            'foo' => 45,
        );
    }
}
