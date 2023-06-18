<?php

namespace PHPMD\Cache;

use PHPMD\AbstractTest;
use PHPMD\Cache\Model\ResultCacheKey;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\RuleSet;
use PHPMD\TextUI\CommandLineOptions;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use ReflectionProperty;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheEngineFactory
 * @covers ::__construct
 */
class ResultCacheEngineFactoryTest extends AbstractTest
{
    /** @var CommandLineOptions&MockObject */
    private $options;

    /** @var ResultCacheKeyFactory&MockObject */
    private $keyFactory;

    /** @var ResultCacheStateFactory&MockObject */
    private $stateFactory;

    /** @var ResultCacheUpdater */
    private $engineFactory;

    protected function setUp()
    {
        $this->options      = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\TextUI\CommandLineOptions')->disableOriginalConstructor()
        );
        $this->keyFactory   = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\Cache\ResultCacheKeyFactory')->disableOriginalConstructor()
        );
        $this->stateFactory = $this->getMockFromBuilder(
            $this->getMockBuilder('\PHPMD\Cache\ResultCacheStateFactory')->disableOriginalConstructor()
        );

        $this->engineFactory = new ResultCacheEngineFactory($this->keyFactory, $this->stateFactory);
    }

    /**
     * @covers ::create
     */
    public function testCreateNotEnabledShouldReturnNull()
    {
        $this->options->expects(self::once())->method('isCacheEnabled')->willReturn(false);
        $this->keyFactory->expects(self::never())->method('create');

        static::assertNull($this->engineFactory->create('/base/path/', $this->options, array()));
    }

    /**
     * @covers ::create
     */
    public function testCreateCacheMissShouldHaveNoOriginalState()
    {
        $ruleSetList = array(new RuleSet());
        $cacheKeyA   = new ResultCacheKey(true, 'baseline', array(), array(), 123);
        $cacheKeyB   = new ResultCacheKey(false, 'baseline', array(), array(), 321);
        $state       = new ResultCacheState($cacheKeyB, array());

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
        $ruleSetList = array(new RuleSet());
        $cacheKey    = new ResultCacheKey(true, 'baseline',array(), array(), 123);
        $state       = new ResultCacheState($cacheKey, array());

        $this->options->expects(self::once())->method('isCacheEnabled')->willReturn(true);
        $this->options->expects(self::once())->method('hasStrict')->willReturn(true);
        $this->options->expects(self::exactly(2))->method('cacheFile')->willReturn('/path/to/cache');

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
