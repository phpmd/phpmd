<?php
class testRuleDoesNotApplyToPrivateArrayFieldAccess
{
    private $foo = array();

    private function bar()
    {
        return $this->foo[42];
    }
}
