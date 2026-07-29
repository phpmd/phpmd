<?php

namespace PHPMD\Cache;

use ArrayIterator;
use PHPMD\AbstractTestCase;
use PHPMD\Cache\Model\ResultCacheState;
use PHPMD\ProcessingError;
use PHPMD\RuleSet;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @coversDefaultClass \PHPMD\Cache\ResultCacheUpdater
 * @covers ::__construct
 */
class ResultCacheUpdaterTest extends AbstractTestCase
{
    /** @var MockObject&ResultCacheState */
    private $state;

    private ResultCacheUpdater $updater;

    protected function setUp(): void
    {
        $this->state = $this->getMockBuilder(ResultCacheState::class)->disableOriginalConstructor()->getMock();

        $this->updater = new ResultCacheUpdater(new NullOutput(), '/base/path/');
    }

    /**
     * @covers ::update
     */
    public function testUpdate(): void
    {
        $ruleSet = new RuleSet();
        $report = $this->getReportMock();
        $violationA = $this->getRuleViolationMock('/base/path/violation/a');
        $violationB = $this->getRuleViolationMock('/base/path/violation/b');
        $errorA = new ProcessingError('Error in file "/base/path/error/a"');
        $errorB = new ProcessingError('Error in file "/base/path/error/b"');

        $report->expects(static::once())->method('getRuleViolations')->willReturn(new ArrayIterator([$violationA]));
        $report->expects(static::once())->method('getErrors')->willReturn(new ArrayIterator([$errorA]));
        $this->state->expects(static::once())
            ->method('getRuleViolations')
            ->with('/base/path/', [$ruleSet])
            ->willReturn([$violationB]);
        $this->state->expects(static::once())
            ->method('getProcessingErrors')
            ->willReturn([$errorB]);

        // expect ViolationB be added to the report
        $report->expects(static::once())->method('addRuleViolation')->with($violationB);

        // expect ErrorB be added to the report
        $report->expects(static::once())->method('addError')->with($errorB);

        // expect ViolationA be added to the state
        $this->state->expects(static::once())->method('addRuleViolation')->with('violation/a', $violationA);

        // expect ErrorA be added to the state
        $this->state->expects(static::once())->method('addError')->with('error/a', $errorA);

        $state = $this->updater->update([$ruleSet], $this->state, $report);
        static::assertSame($this->state, $state);
    }

    /**
     * @covers ::update
     */
    public function testUpdateShouldNotCacheErrorsWithoutFile(): void
    {
        $ruleSet = new RuleSet();
        $report = $this->getReportWithNoViolation();
        $error = new ProcessingError('Error without any file reference');

        $report->expects(static::once())->method('getRuleViolations')->willReturn(new ArrayIterator([]));
        $report->expects(static::once())->method('getErrors')->willReturn(new ArrayIterator([$error]));
        $this->state->expects(static::once())->method('getRuleViolations')->willReturn([]);
        $this->state->expects(static::once())->method('getProcessingErrors')->willReturn([]);

        // expect the error not to be added to the state, as it cannot be attributed to a file
        $this->state->expects(static::never())->method('addError');

        $this->updater->update([$ruleSet], $this->state, $report);
    }
}
