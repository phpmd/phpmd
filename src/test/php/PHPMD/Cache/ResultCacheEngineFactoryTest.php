<?php

namespace PHPMD\Cache;

use PHPMD\AbstractTestCase;
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Cache\Model\ResultCacheStrategy;
use PHPMD\Console\NullOutput;
use PHPMD\RuleSet;
use PHPMD\TextUI\CommandLineOptions;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionProperty;
use Throwable;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheEngineFactory
 * @covers ::__construct
 */
class ResultCacheEngineFactoryTest extends AbstractTestCase
{
    /** @var CommandLineOptions&MockObject */
    private $options;

    /** @var MockObject&ResultCacheKeyFactory */
    private $keyFactory;

    /** @var MockObject&ResultCacheStateFactory */
    private $stateFactory;

    private ResultCacheEngineFactory $engineFactory;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        $this->options = $this->getMockBuilder(CommandLineOptions::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->keyFactory = $this->getMockBuilder(ResultCacheKeyFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->stateFactory = $this->getMockBuilder(ResultCacheStateFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->engineFactory = new ResultCacheEngineFactory(new NullOutput(), $this->keyFactory, $this->stateFactory);
    }

    /**
     * @throws Throwable
     * @covers ::create
     */
    public function testCreateNotEnabledShouldReturnNull(): void
    {
        $this->options->expects(static::once())->method('isCacheEnabled')->willReturn(false);
        $this->keyFactory->expects(static::never())->method('create');

        static::assertNull($this->engineFactory->create('/base/path/', $this->options, []));
    }

    /**
     * @throws Throwable
     * @covers ::create
     */
    public function testCreateCacheMissShouldHaveNoOriginalState(): void
    {
        $ruleSetList = [new RuleSet()];
        $cacheKeyA = new ResultCacheKey(true, 'baseline', [], [], 123);
        $cacheKeyB = new ResultCacheKey(false, 'baseline', [], [], 321);
        $state = new ResultCacheState($cacheKeyB, []);

        $this->options->expects(static::once())->method('cacheStrategy')->willReturn(ResultCacheStrategy::Content);
        $this->options->expects(static::once())->method('isCacheEnabled')->willReturn(true);
        $this->options->expects(static::once())->method('hasStrict')->willReturn(true);
        $this->options->expects(static::exactly(2))->method('cacheFile')->willReturn('/path/to/cache');

        $this->keyFactory->expects(static::once())->method('create')->with(true, $ruleSetList)->willReturn($cacheKeyA);
        $this->stateFactory->expects(static::once())->method('fromFile')->with('/path/to/cache')->willReturn($state);

        $engine = $this->engineFactory->create('/base/path/', $this->options, $ruleSetList);
        static::assertNotNull($engine);
        static::assertNull($this->getFileFilterState($engine->getFileFilter()));
    }

    /**
     * @throws Throwable
     * @covers ::create
     */
    public function testCreateCacheHitShouldHaveOriginalState(): void
    {
        $ruleSetList = [new RuleSet()];
        $cacheKey = new ResultCacheKey(true, 'baseline', [], [], 123);
        $state = new ResultCacheState($cacheKey, []);

        $this->options->expects(static::once())->method('cacheStrategy')->willReturn(ResultCacheStrategy::Content);
        $this->options->expects(static::once())->method('isCacheEnabled')->willReturn(true);
        $this->options->expects(static::once())->method('hasStrict')->willReturn(true);
        $this->options->expects(static::exactly(3))->method('cacheFile')->willReturn('/path/to/cache');

        $this->keyFactory->expects(static::once())->method('create')->with(true, $ruleSetList)->willReturn($cacheKey);
        $this->stateFactory->expects(static::once())->method('fromFile')->with('/path/to/cache')->willReturn($state);

        $engine = $this->engineFactory->create('/base/path/', $this->options, $ruleSetList);
        static::assertNotNull($engine);
        static::assertNotNull($this->getFileFilterState($engine->getFileFilter()));
    }

    /**
     * @throws Throwable
     */
    private function getFileFilterState(ResultCacheFileFilter $filter): ?ResultCacheState
    {
        $property = new ReflectionProperty($filter, 'state');
        $property->setAccessible(true);

        $value = $property->getValue($filter);
        if (!$value) {
            return null;
        }
        static::assertInstanceOf(ResultCacheState::class, $value);

        return $value;
    }
}
