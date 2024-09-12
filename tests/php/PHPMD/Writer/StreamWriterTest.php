<?php

namespace PHPMD\Writer;

use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @coversDefaultClass \PHPMD\Writer\StreamWriter
 * @covers ::__construct
 */
class StreamWriterTest extends TestCase
{
    /**
     * @throws Throwable
     * @covers ::getStream
     */
    public function testGetStream(): void
    {
        $writer = new StreamWriter(STDOUT);
        static::assertSame(STDOUT, $writer->getStream());
    }
}
