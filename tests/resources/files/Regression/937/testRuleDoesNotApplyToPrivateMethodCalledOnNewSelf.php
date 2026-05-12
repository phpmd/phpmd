<?php

class testRuleDoesNotApplyToPrivateMethodCalledOnNewSelf
{
    public static function create($bar)
    {
        $x = new self();
        $x->privateMethod($bar);

        return $x;
    }

    private function privateMethod($bar)
    {
        return $bar;
    }
}
