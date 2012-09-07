<?php
class testRuleDoesNotApplyToPrivateStringIndexFieldAccess
{
    private $foo = "Manuel";

    public function bar()
    {
        return $this->foo{3};
    }
}
