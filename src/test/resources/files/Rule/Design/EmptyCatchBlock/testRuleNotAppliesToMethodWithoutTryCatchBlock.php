<?php

namespace PHPMDTest;

class Foo
{
    public function testRuleNotAppliesToMethodWithoutTryCatchBlock()
    {
        $foo = 'bar';
        $abc = 'xyz';
        if ($foo === 'baz') {
            $abc = 'def';
        }

        return $abc;
    }
}
