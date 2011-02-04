<?php
class testRuleNotAppliesToMethodStartingWithHas
{
    /**
     * @return boolean
     */
    function hasX() {}
}