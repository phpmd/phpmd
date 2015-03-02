<?php
class testRuleDoesNotApplyToMagicMethod
{
    public function __call($name, $args)
    {
        return $name;
    }
}
