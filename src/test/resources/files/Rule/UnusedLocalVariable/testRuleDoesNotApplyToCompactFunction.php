<?php
class testRuleDoesNotApplyToCompactFunction
{
    public function testRuleDoesNotApplyToCompactFunction()
    {
        $key = 'ok';
        return compact('key');
    }
}
