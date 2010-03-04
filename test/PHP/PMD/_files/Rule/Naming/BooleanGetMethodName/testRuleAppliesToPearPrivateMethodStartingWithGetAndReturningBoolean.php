<?php
class testRuleAppliesToPearPrivateMethodStartingWithGetAndReturningBoolean
{
    /**
     * @return boolean
     */
    private function _getFooBar()
    {

    }
}