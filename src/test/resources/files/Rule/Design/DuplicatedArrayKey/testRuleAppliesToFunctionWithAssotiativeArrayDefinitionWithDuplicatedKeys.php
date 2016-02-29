<?php

function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedKeys()
{
    $array = [
        'foo' => 42,
        'foo' => 43,
    ];
}
