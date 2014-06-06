<?php

namespace PHPMDTest;

class test_namespaced_compact_function_rule_works_case_insensitive
{
    public function test_namespaced_compact_function_rule_works_case_insensitive($foo, $bar)
    {
        return Compact('foo', 'bar');
    }
}
