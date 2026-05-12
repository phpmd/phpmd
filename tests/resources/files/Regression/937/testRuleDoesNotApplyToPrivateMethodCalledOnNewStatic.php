<?php

class testRuleDoesNotApplyToPrivateMethodCalledOnNewStatic
{
    public static function create($bar)
    {
        $x = new static();
        $x->privateMethod($bar);

        return $x;
    }

    private function privateMethod($bar)
    {
        return $bar;
    }
}
