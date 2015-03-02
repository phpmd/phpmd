<?php
class testRuleAppliesToUnusedForeachKeyWhenNotIgnored
{
    public function testRuleAppliesToUnusedForeachKeyWhenNotIgnored()
    {
        foreach ($this->index as $key => $value) {
            self::$string{$value} = 'a';
        }
    }
}