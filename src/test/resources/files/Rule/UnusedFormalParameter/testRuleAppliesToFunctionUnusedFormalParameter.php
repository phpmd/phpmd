<?php
function testRuleAppliesToFunctionUnusedFormalParameter($a, $b, $c)
{
    $b = $c;
}