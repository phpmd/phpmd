<?php

function testRuleAppliesToFunctionWithAssotiativeArrayDefinitionWithDuplicatedKeys()
{
    return array(
        'foo' => 42,
        'foo' => 43,
    );
}
