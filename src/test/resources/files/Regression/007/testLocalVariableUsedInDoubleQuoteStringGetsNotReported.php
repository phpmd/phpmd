<?php
class testLocalVariableUsedInDoubleQuoteStringGetsNotReported
{
    public function testLocalVariableUsedInDoubleQuoteStringGetsNotReported()
    {
        $usedVar = "foobar";

        echo "Testing {$usedVar} inside a double quoted string";
    }
}