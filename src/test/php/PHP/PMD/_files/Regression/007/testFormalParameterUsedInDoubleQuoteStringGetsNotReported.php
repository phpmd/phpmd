<?php
class testFormalParameterUsedInDoubleQuoteStringGetsNotReported
{
    public function testFormalParameterUsedInDoubleQuoteStringGetsNotReported($usedParam)
    {
        echo "Testing {$usedParam} inside a double quoted string";
    }
}