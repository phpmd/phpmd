<?php
class testRuleDoesNotApplyToLocalVariableUsedAsArrayIndex
{
    public function testRuleDoesNotApplyToLocalVariableUsedAsArrayIndex()
    {
        foreach ($this->keys as $key) {
            self::$values[$key] = 42;
        }
    }
}