<?php

use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Rule\Design\CouplingBetweenObjects;
use PHPMD\Rule\UnusedLocalVariable;
use PHPMD\Rule\UnusedPrivateMethod;
use PHPMD\Rule\UnusedSuppression;

#[SuppressWarnings(UnusedPrivateMethod::class)]
class UnusedSuppressions
{
    #[SuppressWarnings(UnusedLocalVariable::class)]
    public function nothingUnused(): int
    {
        $used = 42;

        return $used;
    }

    #[SuppressWarnings(CouplingBetweenObjects::class)]
    public function ruleIsNotActive(): void
    {
    }

    #[SuppressWarnings]
    public function withoutRule(): void
    {
    }

    #[SuppressWarnings('NoSuchRule')]
    public function unknownRule(): void
    {
    }
}

#[SuppressWarnings(UnusedSuppression::class)]
class KnowinglyUnusedSuppressions
{
    #[SuppressWarnings(UnusedLocalVariable::class)]
    public function keptByClass(): void
    {
    }
}

#[SuppressWarnings(UnusedSuppression::class)]
class UnusedSuppressionOfUnusedSuppressions
{
}

class KnowinglyUnusedSuppressionOnMethod
{
    #[SuppressWarnings(UnusedLocalVariable::class)]
    #[SuppressWarnings(UnusedSuppression::class)]
    public function keptByMethod(): void
    {
    }
}

/**
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
function unusedSuppressionInDocComment(): void
{
}
