<?php

function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys()
{
    $array = [
        123 => 42,
        '123' => 42,
    ];
}
