<?php
function testRuleDoesNotApplyToInnerFunctionDeclaration($x, $y, $z)
{
    function z_testRuleDoesNotApplyToInnerFunctionDeclaration(
        $a, 
        $b, 
        $c
    ) {

    }
    return ($x + $y + $z);
}
