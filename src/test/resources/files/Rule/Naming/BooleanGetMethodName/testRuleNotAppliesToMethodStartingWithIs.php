<?php
class testRuleNotAppliesToMethodStartingWithIs
{
    /**
     * @return boolean
     */
    function isBaz() {}
}