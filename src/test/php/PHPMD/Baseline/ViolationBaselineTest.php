<?php

namespace PHPMD\Baseline;

use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @coversDefaultClass \PHPMD\Baseline\ViolationBaseline
 */
class ViolationBaselineTest extends TestCase
{
    /**
     * @throws Throwable
     * @covers ::__construct
     * @covers ::getRuleName
     */
    public function testGetRuleName(): void
    {
        $violation = new ViolationBaseline('rule', 'foobar', null);
        static::assertSame('rule', $violation->getRuleName());
    }

    /**
     * Test the give file matches the baseline correctly
     *
     * @throws Throwable
     * @covers ::__construct
     * @covers ::matches
     */
    public function testMatchesWithMethod(): void
    {
        $violation = new ViolationBaseline('sniff', 'foobar.txt', 'method');
        static::assertTrue($violation->matches('foobar.txt', 'method'));
        static::assertTrue($violation->matches('/test/foobar.txt', 'method'));
        static::assertFalse($violation->matches('foo.txt', 'method'));
        static::assertFalse($violation->matches('foobar.txt', 'unknown'));
    }

    /**
     * Test the give file matches the baseline correctly
     *
     * @throws Throwable
     * @covers ::__construct
     * @covers ::matches
     */
    public function testMatchesWithoutMethod(): void
    {
        $violation = new ViolationBaseline('sniff', 'foobar.txt', null);
        static::assertTrue($violation->matches('foobar.txt', null));
        static::assertTrue($violation->matches('/test/foobar.txt', null));
        static::assertFalse($violation->matches('foobar.txt', 'method'));
        static::assertFalse($violation->matches('/test/unknown.txt', null));
    }
}
