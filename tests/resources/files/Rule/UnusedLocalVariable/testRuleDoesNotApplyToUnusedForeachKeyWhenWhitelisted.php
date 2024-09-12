<?php

class testRuleDoesNotApplyToUnusedForeachKeyWhenWhitelisted
{
    public function testRuleDoesNotApplyToUnusedForeachKeyWhenWhitelisted()
    {
        foreach ($this->index as $_ => $value) {
            self::$string{$value} = 'a';
        }
    }
}
