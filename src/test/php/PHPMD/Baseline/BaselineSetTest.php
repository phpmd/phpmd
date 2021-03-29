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
    public function testSetContainsEntry()
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('rule', 'foobar'));

        static::assertTrue($set->contains('rule', 'foobar'));
    }

    /**
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testShouldFindEntryForIdenticalRules()
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('rule', 'foo'));
        $set->addEntry(new ViolationBaseline('rule', 'bar'));

        static::assertTrue($set->contains('rule', 'foo'));
        static::assertTrue($set->contains('rule', 'bar'));
        static::assertFalse($set->contains('rule', 'unknown'));
    }

    /**
     * @covers ::addEntry
     * @covers ::contains
     */
    public function testShouldNotFindEntryForNonExistingRule()
    {
        $set = new BaselineSet();
        $set->addEntry(new ViolationBaseline('rule', 'foo'));

        static::assertFalse($set->contains('unknown', 'foo'));
    }
}
