<?php

namespace PHPMD\Cache;

use PHPMD\AbstractTest;
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Cache\Model\ResultCacheStrategy as Strategy;
use PHPMD\Console\NullOutput;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheFileFilter
 * @covers ::__construct
 */
class ResultCacheFileFilterTest extends AbstractTest
{
    /** @var NullOutput */
    private $output;
    /** @var ResultCacheKey&MockObject */
    private $key;
    /** @var ResultCacheState&MockObject */
    private $state;

    protected function setUp()
    {
        $this->output = new NullOutput();
        $this->key    = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\Cache\Model\ResultCacheKey')->disableOriginalConstructor()
        );
        $this->state  = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\Cache\Model\ResultCacheState')->disableOriginalConstructor()
        );
    }

    /**
     * @covers ::accept
     * @covers ::getState
     */
    public function testAcceptStrategyContentModified()
    {
        $filter = new ResultCacheFileFilter($this->output, __DIR__, Strategy::CONTENT, $this->key, $this->state);

        $this->state->expects(self::once())->method('isFileModified')->willReturn(true);

        static::assertTrue($filter->accept('ResultCacheFileFilterTest.php', __FILE__));
        $state = $filter->getState()->toArray();
        static::assertCount(1, $state['state']['files']);
    }

    /**
     * @covers ::accept
     * @covers ::getState
     */
    public function testAcceptStrategyContentUnmodified()
    {
        $filter = new ResultCacheFileFilter($this->output, __DIR__, Strategy::CONTENT, $this->key, $this->state);

        $this->state->expects(self::once())->method('isFileModified')->willReturn(false);
        $this->state->expects(self::once())->method('getViolations')->willReturn(array('violations'));

        static::assertFalse($filter->accept('ResultCacheFileFilterTest.php', __FILE__));
        $state = $filter->getState()->toArray();
        static::assertCount(1, $state['state']['files']['ResultCacheFileFilterTest.php']['violations']);
    }

    /**
     * @covers ::accept
     * @covers ::getState
     */
    public function testAcceptStrategyTimestampModified()
    {
        $timestamp = (string)filemtime(__FILE__);
        $filter    = new ResultCacheFileFilter($this->output, __DIR__, Strategy::TIMESTAMP, $this->key, $this->state);

        $this->state->expects(self::once())->method('isFileModified')->willReturn(true);

        static::assertTrue($filter->accept('ResultCacheFileFilterTest.php', __FILE__));
        $state = $filter->getState()->toArray();
        static::assertSame($timestamp, $state['state']['files']['ResultCacheFileFilterTest.php']['hash']);
    }

    /**
     * @covers ::accept
     * @covers ::getState
     */
    public function testAcceptWithoutState()
    {
        $filter = new ResultCacheFileFilter($this->output, __DIR__, Strategy::CONTENT, $this->key, null);

        $this->state->expects(self::never())->method('isFileModified')->willReturn(false);

        static::assertTrue($filter->accept('ResultCacheFileFilterTest.php', __FILE__));
        $state = $filter->getState()->toArray();
        static::assertArrayHasKey('hash', $state['state']['files']['ResultCacheFileFilterTest.php']);
    }

    /**
     * @covers ::accept
     */
    public function testAcceptShouldCacheResults()
    {
        $filter = new ResultCacheFileFilter($this->output, __DIR__, Strategy::CONTENT, $this->key, $this->state);

        // expect one invocation
        $this->state->expects(self::once())->method('isFileModified')->willReturn(true);

        // call twice
        static::assertTrue($filter->accept('ResultCacheFileFilterTest.php', __FILE__));
        static::assertTrue($filter->accept('ResultCacheFileFilterTest.php', __FILE__));
    }
}
