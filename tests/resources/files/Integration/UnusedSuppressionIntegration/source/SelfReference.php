<?php

namespace PHPMD\Rule\Naming;

use PHPMD\Attribute\SuppressWarnings;

class LongVariable
{
    #[SuppressWarnings(self::class)]
    public function foo(): void
    {
        $thisVariableNameIsFarTooLong = 42;
        echo $thisVariableNameIsFarTooLong;
    }
}
