<?php

function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedMixedTypeKeys()
{
    return array(
        123 => 42,
        '123' => 42,
    );
}
