<?php

namespace PHPMD\Baseline;

use PHPMD\AbstractTest;

/**
 * @coversDefaultClass \PHPMD\Baseline\BaselineSet
 */
class BaselineSetTest extends AbstractTest
{
    /**
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testSetContainsEntryWithoutMethodName()
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('rule', 'foobar', null));

        static::assertTrue($set->contains('rule', 'foobar', null));
    }

    /**
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testSetContainsEntryWithMethodName()
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('rule', 'foobar', 'method'));

        static::assertTrue($set->contains('rule', 'foobar', 'method'));
    }

    /**
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testShouldFindEntryForIdenticalRules()
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
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testShouldNotFindEntryForNonExistingRule()
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('rule', 'foo', null));

        static::assertFalse($set->contains('unknown', 'foo', null));
    }

    /**
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testShouldNotFindEntryForNonExistingMethod()
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('rule', 'foo', 'method'));

        static::assertFalse($set->contains('rule', 'foo', 'unknown'));
    }
}
