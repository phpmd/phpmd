<?php

class testRuleDoesNotApplyToUsedCatchVariable
{
    public function testRuleDoesNotApplyToUsedCatchVariable()
    {
        try {
            // ...
        } catch (Throwable $exception) {
            echo $exception;
        }
    }
}
