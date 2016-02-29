<?php

function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedQuotedKeys()
{
    $array = [
        'foo' => 42,
        "foo" => 43,
    ];
}
