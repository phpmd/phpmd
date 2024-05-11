<?php

namespace PHPMD\Console;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PHPMD\Console\StreamOutput
 */
class StreamOutputTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::doWrite
     */
    public function testDoWrite(): void
    {
        $stream = fopen('php://memory', 'w+b');
        $output = new StreamOutput($stream);
        $output->write('message');

        fseek($stream, 0);
        static::assertSame('message', fread($stream, 1024));
    }
}
