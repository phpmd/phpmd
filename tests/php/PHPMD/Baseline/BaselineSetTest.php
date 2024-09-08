<?php

namespace PHPMD\Baseline;

use PHPMD\AbstractTestCase;
use Throwable;

/**
 * @coversDefaultClass \PHPMD\Baseline\BaselineSet
 */
class BaselineSetTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testSetContainsEntryWithoutMethodName(): void
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('rule', 'foobar', null));

        static::assertTrue($set->contains('rule', 'foobar', null));
    }

    /**
     * @throws Throwable
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testSetContainsEntryWithMethodName(): void
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('rule', 'foobar', 'method'));

        static::assertTrue($set->contains('rule', 'foobar', 'method'));
    }

    /**
     * @throws Throwable
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testShouldFindEntryForIdenticalRules(): void
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('rule', 'foo', null));
        $set->addEntry(new ViolationBaseline('rule', 'bar', null));
        $set->addEntry(new ViolationBaseline('rule', 'bar', 'method'));

        static::assertTrue($set->contains('rule', 'foo', null));
        static::assertTrue($set->contains('rule', 'bar', null));
        static::assertTrue($set->contains('rule', 'bar', 'method'));
        static::assertFalse($set->contains('rule', 'unknown', null));
    }

    /**
     * @throws Throwable
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testShouldNotFindEntryForNonExistingRule(): void
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('rule', 'foo', null));

        static::assertFalse($set->contains('unknown', 'foo', null));
    }

    /**
     * @throws Throwable
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testShouldNotFindEntryForNonExistingMethod(): void
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('rule', 'foo', 'method'));

        static::assertFalse($set->contains('rule', 'foo', 'unknown'));
    }
}
