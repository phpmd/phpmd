<?php

namespace PHPMD\Cache;

use PHPMD\AbstractTestCase;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheEngine
 * @covers ::__construct
 */
class ResultCacheEngineTest extends AbstractTestCase
{
    /**
     * @covers ::getFileFilter
     * @covers ::getUpdater
     * @covers ::getWriter
     */
    public function testGetters()
    {
        $filter  = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\Cache\ResultCacheFileFilter')->disableOriginalConstructor()
        );
        $updater = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\Cache\ResultCacheUpdater')->disableOriginalConstructor()
        );
        $writer  = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\Cache\ResultCacheWriter')->disableOriginalConstructor()
        );

        $engine = new ResultCacheEngine($filter, $updater, $writer);

        static::assertSame($filter, $engine->getFileFilter());
        static::assertSame($updater, $engine->getUpdater());
        static::assertSame($writer, $engine->getWriter());
    }
}
