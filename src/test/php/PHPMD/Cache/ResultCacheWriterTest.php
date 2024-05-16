<?php

namespace PHPMD\Cache;

use org\bovigo\vfs\vfsStream;
use PHPMD\AbstractTestCase;
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheWriter
 * @covers ::__construct
 */
class ResultCacheWriterTest extends AbstractTestCase
{
    /** @var string */
    private $filePath;

    /** @var ResultCacheWriter */
    private $writer;

    protected function setUp(): void
    {
        $this->filePath = vfsStream::setup()->url() . '/.result-cache.php';
        $this->writer = new ResultCacheWriter($this->filePath);
    }

    /**
     * @covers ::write
     */
    public function testWrite(): void
    {
        $cacheKey = new ResultCacheKey(true, 'baseline', [], [], 70000);
        $cacheState = new ResultCacheState($cacheKey, []);

        $this->writer->write($cacheState);
        static::assertFileExists($this->filePath);

        $data = require $this->filePath;
        static::assertIsArray($data);
        static::assertSame(['key', 'state'], array_keys($data));
    }
}
