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
     * @covers ::getMethodName
     */
    public function testAccessorsWithoutMethod()
    {
        $violation = new ViolationBaseline('rule', 'foobar', null);
        static::assertSame('rule', $violation->getRuleName());
        static::assertSame('foobar', $violation->getFileName());
        static::assertNull($violation->getMethodName());
    }

    /**
     * @covers ::__construct
     * @covers ::getMethodName
     */
    public function testAccessorsWithMethod()
    {
        $violation = new ViolationBaseline('rule', 'foobar', 'method');
        static::assertSame('method', $violation->getMethodName());
    }
}
