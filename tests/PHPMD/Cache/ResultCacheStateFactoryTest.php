<?php

namespace PHPMD\Cache;

use PHPMD\AbstractTest;
use PHPMD\Cache\Model\ResultCacheKey;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheStateFactory
 */
class ResultCacheStateFactoryTest extends AbstractTest
{
    /** @var ResultCacheStateFactory */
    private $factory;

    protected function setUp()
    {
        $this->factory = new ResultCacheStateFactory();
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileNonExisting()
    {
        $state = $this->factory->fromFile('foobar');
        static::assertNull($state);
    }

    /**
     * @covers ::fromFile
     * @covers ::createCacheKey
     */
    public function testFromFileEmptyCache()
    {
        $state = $this->factory->fromFile(static::createResourceUriForTest('.invalid-cache.php'));
        static::assertNull($state);
    }

    /**
     * @covers ::fromFile
     * @covers ::createCacheKey
     */
    public function testFromFileIncompleteCacheKey()
    {
        $state = $this->factory->fromFile(static::createResourceUriForTest('.incomplete-cache.php'));
        static::assertNull($state);
    }

    /**
     * @covers ::fromFile
     * @covers ::createCacheKey
     */
    public function testFromFileFullCache()
    {
        $state = $this->factory->fromFile(static::createResourceUriForTest('.result-cache.php'));

        // assert cache key
        $expectedKey = new ResultCacheKey(
            false,
            'baseline',
            array('rule' => 'hash'),
            array('composer.json' => 'hash1', 'composer.lock' => 'hash2'),
            70000
        );
        $cacheKey    = $state->getCacheKey();
        static::assertEquals($expectedKey, $cacheKey);

        // assert file state
        static::assertFalse($state->isFileModified('file1', 'file1-hash'));
        static::assertTrue($state->isFileModified('file2', 'file1-hash'));
        static::assertSame(array('violations'), $state->getViolations('file2'));
    }
}
