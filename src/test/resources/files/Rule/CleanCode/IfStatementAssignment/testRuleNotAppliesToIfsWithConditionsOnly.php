<?php

namespace PHPMDTest;

class Foo
{

    public function testRuleNotAppliesToIfsWithConditionsOnly()
    {
        $foo = 'bar';

        if ($foo == 'bar') {
            // not applied
        }
        if ($foo === 'bar') {
            // not applied
        }
        if ($foo != 'bar') {
            // not applied
        }
        if ($foo !== 'bar') {
            // not applied
        }
        if ($foo > 1) {
            // not applied
        }
        if ($foo >= 1) {
            // not applied
        }
        if ($foo < 1) {
            // not applied
        }
        if ($foo <= 1) {
            // not applied
        }
    }
}
