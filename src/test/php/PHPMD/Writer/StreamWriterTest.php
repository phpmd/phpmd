<?php

namespace PHPMD\Writer;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PHPMD\Writer\StreamWriter
 *
 * @covers ::__construct
 */
class StreamWriterTest extends TestCase
{
    /**
     * @covers ::getStream
     */
    public function testGetStream()
    {
        $writer = new StreamWriter(STDOUT);
        static::assertSame(STDOUT, $writer->getStream());
    }
}
