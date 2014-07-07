<?php
class test_compact_function_rule_does_not_apply
{
    public function test_compact_function_rule_does_not_apply($foo, $bar)
    {
        return compact('foo', 'bar');
    }
}
