<?php
class testCompactFunctionRuleWorksCaseInsensitive
{
    public function testCompactFunctionRuleWorksCaseInsensitive($foo, $bar)
    {
        return Compact('foo', 'bar');
    }
}
