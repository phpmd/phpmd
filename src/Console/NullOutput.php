<?php

namespace PHPMD\Console;

final class NullOutput extends Output
{
    protected function doWrite(string $message): void
    {
        // do nothing
    }
}
