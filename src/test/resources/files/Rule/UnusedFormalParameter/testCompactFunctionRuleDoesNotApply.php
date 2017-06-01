<?php
class testCompactFunctionRuleDoesNotApply
{
    public function testCompactFunctionRuleDoesNotApply($foo, $bar)
    {
        return compact('foo', 'bar');
    }
}
