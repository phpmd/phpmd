<?php
class test_compact_function_rule_only_applies_to_used_parameters
{
    public function test_compact_function_rule_only_applies_to_used_parameters($foo, $bar, $baz)
    {
        return compact('bar');
    }
}
