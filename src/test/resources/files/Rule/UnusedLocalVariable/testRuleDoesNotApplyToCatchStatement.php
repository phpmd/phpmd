<?php
class testRuleDoesNotApplyToCatchStatement
{
    public function testRuleDoesNotApplyToCatchStatement()
    {
        try {
        } catch (Exception $e) {
        }
    }
}
?>