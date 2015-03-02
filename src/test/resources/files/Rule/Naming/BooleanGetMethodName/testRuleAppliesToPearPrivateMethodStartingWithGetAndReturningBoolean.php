<?php
class testRuleAppliesToPearPrivateMethodStartingWithGetAndReturningBoolean
{
    /**
     * @return boolean
     */
    private function getFooBar()
    {

    }
}
