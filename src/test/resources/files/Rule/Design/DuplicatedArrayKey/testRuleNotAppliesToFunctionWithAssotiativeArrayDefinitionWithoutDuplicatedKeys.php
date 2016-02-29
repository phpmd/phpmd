<?php

function testRuleNotAppliesToFunctionWithAssotiativeArrayDefinitionWithoutDuplicatedKeys()
{
    $array = [
        'foo' => 42,
        'bar' => 43,
    ];
}
