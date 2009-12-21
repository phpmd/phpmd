<?php
class testRuleDoesNotApplyToDynamicProperty
{
    function testRuleDoesNotApplyToDynamicProperty()
    {
        $x = 'foo';
        $this->$x = 42;
    }
}