<?php

namespace PHPMD\Console;

final class NullOutput extends Output
{
    /**
     * @inheritDoc
     */
    protected function doWrite($message): void
    {
        // do nothing
    }
}
