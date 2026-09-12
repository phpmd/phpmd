<?php

class testRuleDoesNotApplyToUsedForeachValue
{
    public function testRuleDoesNotApplyToUsedForeachValue($items)
    {
        foreach ($items as $item) {
            echo $item;
        }
    }
}
