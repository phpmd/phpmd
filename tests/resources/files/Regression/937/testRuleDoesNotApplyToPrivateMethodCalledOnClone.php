<?php

class testRuleDoesNotApplyToPrivateMethodCalledOnClone
{

    private $keywords = [];

    public function testClone()
    {
        $clone = clone $this;
        $clone->prepareKeywords();

        return $clone;
    }

    private function prepareKeywords()
    {
        $this->keywords = ['a', 'b'];
    }
}
