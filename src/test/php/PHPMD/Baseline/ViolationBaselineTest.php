<?php

namespace PHPMD\Baseline;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PHPMD\Baseline\ViolationBaseline
 */
class ViolationBaselineTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRuleName
     * @covers ::getFileName
     */
    public function testAccessors()
    {
        $violation = new ViolationBaseline('rule', 'foobar');
        static::assertSame('rule', $violation->getRuleName());
        static::assertSame('foobar', $violation->getFileName());
    }
}
