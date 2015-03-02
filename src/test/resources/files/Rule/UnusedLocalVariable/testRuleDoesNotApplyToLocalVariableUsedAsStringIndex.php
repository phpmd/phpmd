<?php
class testRuleDoesNotApplyToLocalVariableUsedAsStringIndex
{
    public function testRuleDoesNotApplyToLocalVariableUsedAsStringIndex()
    {
        foreach ($this->index as $idx) {
            self::$string{$idx} = 'a';
        }
    }
}