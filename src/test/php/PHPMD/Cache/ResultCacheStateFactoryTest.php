<?php

namespace PHPMD\Cache;

use PHPMD\AbstractTestCase;
use PHPMD\Cache\Model\ResultCacheKey;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheStateFactory
 */
class ResultCacheStateFactoryTest extends AbstractTestCase
{
    /** @var ResultCacheStateFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = new ResultCacheStateFactory();
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileNonExisting(): void
    {
        $state = $this->factory->fromFile('foobar');
        static::assertNull($state);
    }

    /**
     * @covers ::fromFile
     * @covers ::createCacheKey
     */
    public function testFromFileEmptyCache(): void
    {
        $state = $this->factory->fromFile(static::createResourceUriForTest('.invalid-cache.php'));
        static::assertNull($state);
    }

    /**
     * @covers ::fromFile
     * @covers ::createCacheKey
     */
    public function testFromFileIncompleteCacheKey(): void
    {
        $state = $this->factory->fromFile(static::createResourceUriForTest('.incomplete-cache.php'));
        static::assertNull($state);
    }

    /**
     * @covers ::fromFile
     * @covers ::createCacheKey
     */
    public function testFromFileFullCache(): void
    {
        $state = $this->factory->fromFile(static::createResourceUriForTest('.result-cache.php'));

        // assert cache key
        $expectedKey = new ResultCacheKey(
            false,
            'baseline',
            ['rule' => 'hash'],
            ['composer.json' => 'hash1', 'composer.lock' => 'hash2'],
            70000
        );
        $cacheKey = $state->getCacheKey();
        static::assertEquals($expectedKey, $cacheKey);

        // assert file state
        static::assertFalse($state->isFileModified('file1', 'file1-hash'));
        static::assertTrue($state->isFileModified('file2', 'file1-hash'));
        static::assertSame(['violations'], $state->getViolations('file2'));
    }

    /**
     * @covers ::fromFile
     * @covers ::createCacheKey
     */
    public function testFromFileWithCacheWithoutBaselineOrComposer(): void
    {
        $state = $this->factory->fromFile(static::createResourceUriForTest('.minimal-cache.php'));
        static::assertNotNull($state);

        // assert cache key
        $expectedKey = new ResultCacheKey(
            false,
            null,
            ['rule' => 'hash'],
            [],
            70000
        );
        $cacheKey = $state->getCacheKey();
        static::assertEquals($expectedKey, $cacheKey);
    }
}
