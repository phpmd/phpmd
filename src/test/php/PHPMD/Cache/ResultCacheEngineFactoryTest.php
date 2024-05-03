<?php

namespace PHPMD\Cache;

use PHPMD\AbstractTestCase;
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Console\NullOutput;
use PHPMD\RuleSet;
use PHPMD\TextUI\CommandLineOptions;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use ReflectionProperty;

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

    /** @var ResultCacheUpdater */
    private $engineFactory;

    protected function setUp(): void
    {
        $this->options      = $this->getMockFromBuilder(
            $this->getMockBuilder(CommandLineOptions::class)->disableOriginalConstructor()
        );
        $this->keyFactory   = $this->getMockFromBuilder(
            $this->getMockBuilder(ResultCacheKeyFactory::class)->disableOriginalConstructor()
        );
        $this->stateFactory = $this->getMockFromBuilder(
            $this->getMockBuilder(ResultCacheStateFactory::class)->disableOriginalConstructor()
        );

        $this->engineFactory = new ResultCacheEngineFactory(new NullOutput(), $this->keyFactory, $this->stateFactory);
    }

    /**
     * @covers ::create
     */
    public function testCreateNotEnabledShouldReturnNull()
    {
        $this->options->expects(self::once())->method('isCacheEnabled')->willReturn(false);
        $this->keyFactory->expects(self::never())->method('create');

        static::assertNull($this->engineFactory->create('/base/path/', $this->options, []));
    }

    /**
     * @covers ::create
     */
    public function testCreateCacheMissShouldHaveNoOriginalState()
    {
        $ruleSetList = [new RuleSet()];
        $cacheKeyA   = new ResultCacheKey(true, 'baseline', [], [], 123);
        $cacheKeyB   = new ResultCacheKey(false, 'baseline', [], [], 321);
        $state       = new ResultCacheState($cacheKeyB, []);

        $this->options->expects(self::once())->method('isCacheEnabled')->willReturn(true);
        $this->options->expects(self::once())->method('hasStrict')->willReturn(true);
        $this->options->expects(self::exactly(2))->method('cacheFile')->willReturn('/path/to/cache');

        $this->keyFactory->expects(self::once())->method('create')->with(true, $ruleSetList)->willReturn($cacheKeyA);
        $this->stateFactory->expects(self::once())->method('fromFile')->with('/path/to/cache')->willReturn($state);

        $engine = $this->engineFactory->create('/base/path/', $this->options, $ruleSetList);
        static::assertNotNull($engine);
        static::assertNull($this->getFileFilterState($engine->getFileFilter()));
    }

    /**
     * @covers ::create
     */
    public function testCreateCacheHitShouldHaveOriginalState()
    {
        $ruleSetList = [new RuleSet()];
        $cacheKey    = new ResultCacheKey(true, 'baseline', [], [], 123);
        $state       = new ResultCacheState($cacheKey, []);

        $this->options->expects(self::once())->method('isCacheEnabled')->willReturn(true);
        $this->options->expects(self::once())->method('hasStrict')->willReturn(true);
        $this->options->expects(self::exactly(3))->method('cacheFile')->willReturn('/path/to/cache');

        $this->keyFactory->expects(self::once())->method('create')->with(true, $ruleSetList)->willReturn($cacheKey);
        $this->stateFactory->expects(self::once())->method('fromFile')->with('/path/to/cache')->willReturn($state);

        $engine = $this->engineFactory->create('/base/path/', $this->options, $ruleSetList);
        static::assertNotNull($engine);
        static::assertNotNull($this->getFileFilterState($engine->getFileFilter()));
    }

    /**
     * @return ResultCacheState|null
     */
    private function getFileFilterState(ResultCacheFileFilter $filter)
    {
        $property = new ReflectionProperty($filter, 'state');
        $property->setAccessible(true);

        return $property->getValue($filter);
    }
}
