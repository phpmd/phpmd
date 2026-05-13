<?php

class testRuleDoesNotCrashOnCompoundAssignmentOperators
{
    public function test()
    {
        $test = 1;
        $test2 = $test;
        $test -= $test2;
        echo $test;
    }
}
