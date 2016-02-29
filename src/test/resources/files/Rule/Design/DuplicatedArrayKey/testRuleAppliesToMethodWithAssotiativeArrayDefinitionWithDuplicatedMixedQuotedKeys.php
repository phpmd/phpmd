<?php

class testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys
{
    function testRuleAppliesToMethodWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys()
    {
        return array(
            'foo' => 42,
            "foo" => 42,
        );
    }
}
