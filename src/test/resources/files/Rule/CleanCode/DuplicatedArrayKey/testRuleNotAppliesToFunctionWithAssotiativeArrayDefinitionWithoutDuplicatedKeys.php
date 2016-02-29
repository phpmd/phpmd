<?php

function testRuleNotAppliesToFunctionWithAssotiativeArrayDefinitionWithoutDuplicatedKeys()
{
    return array(
        'foo' => 42,
        'bar' => 43,
    );
}
