<?php
class testRuleDoesNotApplyToPrivateFieldInChainedMethodCall
{
    /**
     * @var SplObjectStorage
     */
    private $foo = null;

    public function add($object)
    {
        $this->foo->attach($object);
    }
}
