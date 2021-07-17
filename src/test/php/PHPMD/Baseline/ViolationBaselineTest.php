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
     */
    public function testGetRuleName()
    {
        $violation = new ViolationBaseline('rule', 'foobar', null);
        static::assertSame('rule', $violation->getRuleName());
    }

    /**
     * Test the give file matches the baseline correctly
     *
     * @covers ::__construct
     * @covers ::matches
     * @return void
     */
    public function testMatchesWithMethod()
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
     * @covers ::__construct
     * @covers ::matches
     * @return void
     */
    public function testMatchesWithoutMethod()
    {
        $violation = new ViolationBaseline('sniff', 'foobar.txt', null);
        static::assertTrue($violation->matches('foobar.txt', null));
        static::assertTrue($violation->matches('/test/foobar.txt', null));
        static::assertFalse($violation->matches('foobar.txt', 'method'));
        static::assertFalse($violation->matches('/test/unknown.txt', null));
    }
}
