<?php
class testRuleDoesNotApplyToThisAccessedPrivateField
{
    private $_foo = 42;

    public function __construct()
    {
        $this->_foo = 23;
    }
}