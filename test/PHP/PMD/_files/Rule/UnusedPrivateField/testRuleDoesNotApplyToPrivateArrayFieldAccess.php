<?php
class testRuleDoesNotApplyToPrivateArrayFieldAccess
{
    private $_foo = array();

    private function bar()
    {
        return $this->_foo[42];
    }
}