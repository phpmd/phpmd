<?php

class testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys
{
    public function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys()
    {
        return array(
            'foo' => 42,
            "foo" => 42,
        );
    }
}
