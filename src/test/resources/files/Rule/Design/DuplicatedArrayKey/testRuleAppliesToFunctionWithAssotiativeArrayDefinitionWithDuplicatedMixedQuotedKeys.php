<?php

function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys()
{
    return array(
        'foo' => 42,
        "foo" => 43,
    );
}
