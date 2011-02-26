<?php
function testInnerFunctionParametersDoNotHideUnusedVariables()
{
    $x = 42;
    function z_testInnerFunctionParametersDoNotHideUnusedVariables($x)
    {
        
    }
}