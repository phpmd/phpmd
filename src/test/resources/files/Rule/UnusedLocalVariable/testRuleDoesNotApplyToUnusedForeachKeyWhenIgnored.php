<?php
class testRuleDoesNotApplyToUnusedForeachKeyWhenIgnored
{
    public function testRuleDoesNotApplyToUnusedForeachKeyWhenIgnored()
    {
        foreach ($this->index as $key => $value) {
            self::$string{$value} = 'a';
        }
    }
}