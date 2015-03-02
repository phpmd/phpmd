<?php
class testRuleDoesNotApplyToThisAccessedPrivateField
{
    private $foo = 42;

    public function __construct()
    {
        $this->foo = 23;
    }
}
