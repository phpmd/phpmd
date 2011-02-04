<?php
class testRuleDoesNotApplyToPrivateStringIndexFieldAccess
{
    private $_foo = "Manuel";

    public function bar()
    {
        return $this->_foo{3};
    }
}