<?php

namespace PHPMD\Test;

use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Rule\UnusedLocalVariable;

class UnusedSuppressWarningsOnMethod
{
    #[SuppressWarnings(UnusedLocalVariable::class)]
    public function shortMethod(): void
    {
        echo 'hello';
    }
}
