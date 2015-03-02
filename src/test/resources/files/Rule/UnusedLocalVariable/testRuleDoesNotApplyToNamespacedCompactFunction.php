<?php

namespace PHPMDTest;

class testRuleDoesNotApplyToNamespacedCompactFunction
{
    public function testRuleDoesNotApplyToNamespacedCompactFunction()
    {
        $key = 'ok';
        return compact('key');
    }
}
