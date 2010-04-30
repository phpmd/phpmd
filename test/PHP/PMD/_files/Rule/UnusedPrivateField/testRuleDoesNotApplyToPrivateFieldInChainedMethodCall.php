<?php
class testRuleDoesNotApplyToPrivateFieldInChainedMethodCall
{
    /**
     * @var SplObjectStorage
     */
    private $_foo = null;

    public function add($object)
    {
        $this->_foo->attach($object);
    }
}