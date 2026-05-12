<?php

namespace PHPMD\Test;

class UnusedSuppressWarningsAnnotationOnMethod
{
    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function shortMethod(): void
    {
        echo 'hello';
    }
}
