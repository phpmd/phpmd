<?php

class testRuleDoesNotApplyToUsedParameterInAnonymousClassMethod
{
    public function testRuleDoesNotApplyToUsedParameterInAnonymousClassMethod(): object
    {
        return new class {
            public function myMethod(string $param): string
            {
                return $param;
            }
        };
    }
}
