<?php

class testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys
{
    function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys()
    {
        $array = [
            'foo' => 42,
            "foo" => 42,
        ];
    }
}
