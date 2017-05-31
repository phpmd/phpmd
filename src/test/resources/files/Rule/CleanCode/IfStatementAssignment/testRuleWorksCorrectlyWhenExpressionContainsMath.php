<?php

namespace PHPMDTest;

class Foo
{

    public function testRuleWorksCorrectlyWhenExpressionContainsMath()
    {
        $foo = 0;
        if ($foo == 1 * 1) {
            // not applied
        }
        if ($foo == 1 % 2) {
            // not applied
        }
        if ($foo == 1 + 2 + 2 / 1) {
            // not applied
        }
        if ($foo == 'foo' . 'bar') {
            // not applied
        }
        if ($foo == ('' . '')) {
            // not applied
        }
        if ($foo = 1 * 1) {
            // applied
        }
        if ($foo = '' . 1) {
            // applied
        }
        if ($foo = (int)1.0 + (float)false % (string)1) {
            // applied
        }
    }
}
