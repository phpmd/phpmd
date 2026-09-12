<?php

class testRuleDoesNotApplyToUsedListDestructuredVariable
{
    public function testRuleDoesNotApplyToUsedListDestructuredVariable($values)
    {
        [$value] = $values;
        echo $value;
    }
}
