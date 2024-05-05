<?php

namespace PHPMD\Cache;

use PHPMD\AbstractTestCase;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\Console\NullOutput;
use PHPMD\RuleSet;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheUpdater
 * @covers ::__construct
 */
class ResultCacheUpdaterTest extends AbstractTestCase
{
    /** @var MockObject&ResultCacheState */
    private $state;

    /** @var ResultCacheUpdater */
    private $updater;

    protected function setUp(): void
    {
        $this->state = $this->getMockFromBuilder(
            $this->getMockBuilder(ResultCacheState::class)->disableOriginalConstructor()
        );

        $this->updater = new ResultCacheUpdater(new NullOutput(), '/base/path/');
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $ruleSet = new RuleSet();
        $report = $this->getReportMock();
        $violationA = $this->getRuleViolationMock('/base/path/violation/a');
        $violationB = $this->getRuleViolationMock('/base/path/violation/b');

        $report->expects(self::once())->method('getRuleViolations')->willReturn([$violationA]);
        $this->state->expects(self::once())
            ->method('getRuleViolations')
            ->with('/base/path/', [$ruleSet])
            ->willReturn([$violationB]);

        // expect ViolationB be added to the report
        $report->expects(self::once())->method('addRuleViolation')->with($violationB);

        // expect ViolationA be added to the state
        $this->state->expects(self::once())->method('addRuleViolation')->with('violation/a', $violationA);

        $state = $this->updater->update([$ruleSet], $this->state, $report);
        static::assertSame($this->state, $state);
    }
}
