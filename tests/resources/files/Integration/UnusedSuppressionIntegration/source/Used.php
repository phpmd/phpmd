<?php

use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Rule\UnusedLocalVariable;
use PHPMD\Rule\UnusedPrivateMethod;

class UsedSuppressions
{
    /** @SuppressWarnings(PHPMD.LongVariable) */
    public $suppressedByDocComment = 42;

    #[SuppressWarnings(UnusedLocalVariable::class)]
    public function onMethod(): void
    {
        $unused = 42;
    }

    #[SuppressWarnings('PHPMD\Rule\UnusedLocalVariable')]
    public function withString(): void
    {
        $unused = 42;
    }

    #[SuppressWarnings]
    public function withoutRule(): void
    {
        $unused = 42;
    }

    #[SuppressWarnings(UnusedLocalVariable::class)]
    public function otherRulesStillReported(int $unusedParameter): void
    {
        $unused = 42;
    }
}

#[SuppressWarnings(UnusedPrivateMethod::class)]
#[SuppressWarnings(UnusedLocalVariable::class)]
class UsedSuppressionsOnClass
{
    public function foo(): void
    {
        $unused = 42;
    }

    private function unused(): void
    {
    }
}

/**
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
function usedSuppressionInDocComment(): void
{
    $unused = 42;
}
