<?php

namespace PHPMDTest;

class test_namespaced_compact_function_rule_only_applies_to_used_parameters
{
    public function test_namespaced_compact_function_rule_only_applies_to_used_parameters($foo, $bar, $baz)
    {
        return compact('bar');
    }
}
