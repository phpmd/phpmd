<?php
class testRuleNotAppliesToMethodWithReturnTypeNotBoolean
{
    /**
     * @return array(boolean)
     */
    function getFooBar() {}
}