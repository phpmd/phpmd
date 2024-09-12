<?php

namespace PHPMD\Console;

use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @coversDefaultClass \PHPMD\Console\StreamOutput
 */
class StreamOutputTest extends TestCase
{
    /**
     * @throws Throwable
     * @covers ::__construct
     * @covers ::doWrite
     */
    public function testDoWrite(): void
    {
        $stream = fopen('php://memory', 'w+b');
        static::assertIsResource($stream);
        $output = new StreamOutput($stream);
        $output->write('message');

        fseek($stream, 0);
        static::assertSame('message', fread($stream, 1024));
    }
}
