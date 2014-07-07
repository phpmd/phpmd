<?php
class test_compact_function_rule_works_case_insensitive
{
    public function test_compact_function_rule_works_case_insensitive($foo, $bar)
    {
        return Compact('foo', 'bar');
    }
}
