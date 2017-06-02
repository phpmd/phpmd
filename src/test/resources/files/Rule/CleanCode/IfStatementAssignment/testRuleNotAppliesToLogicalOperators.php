<?php

namespace PHPMDTest;

class Foo
{

    public function testRuleNotAppliesToLogicalOperators()
    {
        if (1 || 0) { // not applied
            // ...
        }
        if (1 && 1) { // not applied
            // ...
        }
        if (1 or 0) { // not applied
            // ...
        }
        if (1 and 1) { // not applied
            // ...
        }
        if (1 xor 1) { // not applied
            // ...
        }
        if (1 % 1) { // not applied
            // ...
        }
        if (1 || !1) { // not applied
            // ...
        }
        if (!1 % !1) { // not applied
            // ...
        }
    }
}
