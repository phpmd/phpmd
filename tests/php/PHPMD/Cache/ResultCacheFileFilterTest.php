<?php

namespace PHPMD\Cache;

use PHPMD\AbstractTestCase;
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Cache\Model\ResultCacheStrategy as Strategy;
use PHPMD\Console\NullOutput;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheFileFilter
 * @covers ::__construct
 */
class ResultCacheFileFilterTest extends AbstractTestCase
{
    private NullOutput $output;

    /** @var MockObject&ResultCacheKey */
    private $key;

    /** @var MockObject&ResultCacheState */
    private $state;

    protected function setUp(): void
    {
        $this->output = new NullOutput();
        $this->key = $this->getMockBuilder(ResultCacheKey::class)->disableOriginalConstructor()->getMock();
        $this->state = $this->getMockBuilder(ResultCacheState::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @covers ::accept
     * @covers ::getState
     */
    public function testAcceptStrategyContentModified(): void
    {
        $filter = new ResultCacheFileFilter($this->output, __DIR__, Strategy::Content, $this->key, $this->state);

        $this->state->expects(static::once())->method('isFileModified')->willReturn(true);

        static::assertTrue($filter->accept('ResultCacheFileFilterTest.php', __FILE__));
        $state = $filter->getState()->toArray();
        static::assertIsArray($state['state']['files']);
        static::assertCount(1, $state['state']['files']);
    }

    /**
     * @covers ::accept
     * @covers ::getState
     */
    public function testAcceptStrategyContentUnmodified(): void
    {
        $filter = new ResultCacheFileFilter($this->output, __DIR__, Strategy::Content, $this->key, $this->state);

        $this->state->expects(static::once())->method('isFileModified')->willReturn(false);
        $this->state->expects(static::once())->method('getViolations')->willReturn(['violations']);

        static::assertFalse($filter->accept('ResultCacheFileFilterTest.php', __FILE__));
        $state = $filter->getState()->toArray();
        static::assertIsArray($state['state']['files']);
        static::assertIsArray($state['state']['files']['ResultCacheFileFilterTest.php']);
        static::assertIsArray($state['state']['files']['ResultCacheFileFilterTest.php']['violations']);
        static::assertCount(1, $state['state']['files']['ResultCacheFileFilterTest.php']['violations']);
    }

    /**
     * @covers ::accept
     * @covers ::getState
     */
    public function testAcceptStrategyTimestampModified(): void
    {
        $timestamp = (string) filemtime(__FILE__);
        $filter = new ResultCacheFileFilter($this->output, __DIR__, Strategy::Timestamp, $this->key, $this->state);

        $this->state->expects(static::once())->method('isFileModified')->willReturn(true);

        static::assertTrue($filter->accept('ResultCacheFileFilterTest.php', __FILE__));
        $state = $filter->getState()->toArray();
        static::assertIsArray($state['state']['files']);
        static::assertIsArray($state['state']['files']['ResultCacheFileFilterTest.php']);
        static::assertSame($timestamp, $state['state']['files']['ResultCacheFileFilterTest.php']['hash']);
    }

    /**
     * @covers ::accept
     * @covers ::getState
     */
    public function testAcceptWithoutState(): void
    {
        $filter = new ResultCacheFileFilter($this->output, __DIR__, Strategy::Content, $this->key, null);

        $this->state->expects(static::never())->method('isFileModified')->willReturn(false);

        static::assertTrue($filter->accept('ResultCacheFileFilterTest.php', __FILE__));
        $state = $filter->getState()->toArray();
        static::assertIsArray($state['state']['files']);
        static::assertIsArray($state['state']['files']['ResultCacheFileFilterTest.php']);
        static::assertArrayHasKey('hash', $state['state']['files']['ResultCacheFileFilterTest.php']);
    }

    /**
     * @covers ::accept
     */
    public function testAcceptShouldCacheResults(): void
    {
        $filter = new ResultCacheFileFilter($this->output, __DIR__, Strategy::Content, $this->key, $this->state);

        // expect one invocation
        $this->state->expects(static::once())->method('isFileModified')->willReturn(true);

        // call twice
        static::assertTrue($filter->accept('ResultCacheFileFilterTest.php', __FILE__));
        static::assertTrue($filter->accept('ResultCacheFileFilterTest.php', __FILE__));
    }
}
