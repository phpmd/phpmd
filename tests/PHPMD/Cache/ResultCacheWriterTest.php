<?php

namespace PHPMD\Cache;

use org\bovigo\vfs\vfsStream;
use PHPMD\AbstractTest;
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheWriter
 * @covers ::__construct
 */
class ResultCacheWriterTest extends AbstractTest
{
    /** @var string */
    private $filePath;

    /** @var ResultCacheWriter */
    private $writer;

    protected function setUp()
    {
        $this->filePath = vfsStream::setup()->url() . '/.result-cache.php';
        $this->writer   = new ResultCacheWriter($this->filePath);
    }

    /**
     * @covers ::write
     */
    public function testWrite()
    {
        $cacheKey   = new ResultCacheKey(true, 'baseline', array(), array(), 70000);
        $cacheState = new ResultCacheState($cacheKey, array());

        $this->writer->write($cacheState);
        static::assertFileExists($this->filePath);

        $data = require $this->filePath;
        static::assertInternalType('array', $data);
        static::assertSame(array('key', 'state'), array_keys($data));
    }
}
