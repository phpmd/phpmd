<?php

namespace PHPMDTest;

class Foo
{

    public function testRuleNotAppliesToIfsWithoutAssignment()
    {
        $foo = 'bar';

        if (true) { // not applied
            // ...
        } else { // not applied
            // ...
        }
        if (null) { // not applied
            // ...
        }
        if (rand()) { // not applied
            // ...
        }
        if ($foo) { // not applied
            // ...
        }
    }
}
