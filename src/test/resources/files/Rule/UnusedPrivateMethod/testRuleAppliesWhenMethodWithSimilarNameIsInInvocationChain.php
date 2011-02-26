<?php
class testRuleAppliesWhenMethodWithSimilarNameIsInInvocationChain
{
    /**
     * @var SplObjectStorage
     */
    protected $storage = null;

    public function run()
    {
        $this->storage->attach($object);
    }

    private function attach($object)
    {

    }
}