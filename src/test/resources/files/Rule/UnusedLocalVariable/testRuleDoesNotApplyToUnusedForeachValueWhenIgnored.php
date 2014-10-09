<?php
class testRuleDoesNotApplyToUnusedForeachValueWhenIgnored
{
    public function testRuleDoesNotApplyToUnusedForeachValueWhenIgnored()
    {
        foreach ($this->index as $key => $value) {
            self::$string{$key} = 'a';
        }
    }
}