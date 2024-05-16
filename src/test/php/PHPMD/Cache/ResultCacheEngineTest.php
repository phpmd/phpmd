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
    public function testGetters(): void
    {
        $filter = $this->getMockFromBuilder(
            $this->getMockBuilder(ResultCacheFileFilter::class)->disableOriginalConstructor()
        );
        $updater = $this->getMockFromBuilder(
            $this->getMockBuilder(ResultCacheUpdater::class)->disableOriginalConstructor()
        );
        $writer = $this->getMockFromBuilder(
            $this->getMockBuilder(ResultCacheWriter::class)->disableOriginalConstructor()
        );

        $engine = new ResultCacheEngine($filter, $updater, $writer);

        static::assertSame($filter, $engine->getFileFilter());
        static::assertSame($updater, $engine->getUpdater());
        static::assertSame($writer, $engine->getWriter());
    }
}
