<?php
class testCompactFunctionRuleWorksCaseInsensitive
{
    public function testCompactFunctionRuleWorksCaseInsensitive()
    {
        $foo = 1; $bar = 2; $baz = 0;

        return Compact('foo', 'bar', 'baz');
    }
}
