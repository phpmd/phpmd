<?php
class testRuleAppliesToUnusedForeachValueWhenNotIgnored
{
    public function testRuleAppliesToUnusedForeachValueWhenNotIgnored()
    {
        foreach ($this->index as $key => $value) {
            self::$string{$key} = 'a';
        }
    }
}