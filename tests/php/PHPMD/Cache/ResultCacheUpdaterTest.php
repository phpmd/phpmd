<?php

namespace PHPMD\Cache;

use ArrayIterator;
use PHPMD\AbstractTestCase;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Console\NullOutput;
use PHPMD\RuleSet;
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheUpdater
 * @covers ::__construct
 */
class ResultCacheUpdaterTest extends AbstractTestCase
{
    /** @var MockObject&ResultCacheState */
    private $state;

    private ResultCacheUpdater $updater;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        $this->state = $this->getMockBuilder(ResultCacheState::class)->disableOriginalConstructor()->getMock();

        $this->updater = new ResultCacheUpdater(new NullOutput(), '/base/path/');
    }

    /**
     * @throws Throwable
     * @covers ::update
     */
    public function testUpdate(): void
    {
        $ruleSet = new RuleSet();
        $report = $this->getReportMock();
        $violationA = $this->getRuleViolationMock('/base/path/violation/a');
        $violationB = $this->getRuleViolationMock('/base/path/violation/b');

        $report->expects(static::once())->method('getRuleViolations')->willReturn(new ArrayIterator([$violationA]));
        $this->state->expects(static::once())
            ->method('getRuleViolations')
            ->with('/base/path/', [$ruleSet])
            ->willReturn([$violationB]);

        // expect ViolationB be added to the report
        $report->expects(static::once())->method('addRuleViolation')->with($violationB);

        // expect ViolationA be added to the state
        $this->state->expects(static::once())->method('addRuleViolation')->with('violation/a', $violationA);

        $state = $this->updater->update([$ruleSet], $this->state, $report);
        static::assertSame($this->state, $state);
    }
}
