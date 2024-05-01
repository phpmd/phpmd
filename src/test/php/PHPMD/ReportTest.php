<?php

/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license   https://opensource.org/licenses/bsd-license.php BSD License
 * @link      http://phpmd.org/
 */

namespace PHPMD;

use PHPMD\Baseline\BaselineMode;
use PHPMD\Baseline\BaselineSet;
use PHPMD\Baseline\BaselineValidator;
use PHPMD\Baseline\ViolationBaseline;

/**
 * Test case for the report class.
 *
 * @covers \PHPMD\Report
 */
class ReportTest extends AbstractTestCase
{
    /**
     * Tests that the report returns a linear/sorted list of all rule violation
     * files.
     */
    public function testReportReturnsAListWithAllRuleViolations(): void
    {
        $report = new Report();

        $report->addRuleViolation($this->getRuleViolationMock('foo.txt'));
        $report->addRuleViolation($this->getRuleViolationMock('bar.txt'));
        $report->addRuleViolation($this->getRuleViolationMock('foo.txt'));
        $report->addRuleViolation($this->getRuleViolationMock('foo.txt'));
        $report->addRuleViolation($this->getRuleViolationMock('bar.txt'));

        $actual = [];
        foreach ($report->getRuleViolations() as $violation) {
            $actual[] = $violation->getFileName();
        }

        $expected = ['bar.txt', 'bar.txt', 'foo.txt', 'foo.txt', 'foo.txt'];

        static::assertSame($expected, $actual);
    }

    /**
     * Tests that the report returns the result by the violation line number.
     */
    public function testReportSortsResultByLineNumber(): void
    {
        $report = new Report();

        $report->addRuleViolation($this->getRuleViolationMock('foo.txt', 4, 5));
        $report->addRuleViolation($this->getRuleViolationMock('foo.txt', 1, 2));
        $report->addRuleViolation($this->getRuleViolationMock('foo.txt', 3, 6));
        $report->addRuleViolation($this->getRuleViolationMock('foo.txt', 2, 3));
        $report->addRuleViolation($this->getRuleViolationMock('bar.txt', 2, 3));
        $report->addRuleViolation($this->getRuleViolationMock('bar.txt', 1, 2));

        $actual = [];
        foreach ($report->getRuleViolations() as $violation) {
            $actual[] = [
                $violation->getFileName(),
                $violation->getBeginLine(),
                $violation->getEndLine(),
            ];
        }

        $expected = [
            ['bar.txt', 1, 2],
            ['bar.txt', 2, 3],
            ['foo.txt', 1, 2],
            ['foo.txt', 2, 3],
            ['foo.txt', 3, 6],
            ['foo.txt', 4, 5],
        ];

        static::assertSame($expected, $actual);
    }

    /**
     * Tests that the timer method returns the expected result.
     */
    public function testReportTimerReturnsMilliSeconds(): void
    {
        $start = microtime(true);

        $report = new Report();
        $report->start();
        usleep(50000); // 50 Milli Seconds
        $report->end();

        $time = ceil((microtime(true) - $start) * 1000.0);

        // Windows does not compute the time correctly, simply skipping
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            static::assertGreaterThanOrEqual(50, $report->getElapsedTimeInMillis());
        }
        static::assertLessThanOrEqual($time, $report->getElapsedTimeInMillis());
    }

    /**
     * testIsEmptyReturnsTrueByDefault
     */
    public function testIsEmptyReturnsTrueByDefault(): void
    {
        $report = new Report();
        static::assertTrue($report->isEmpty());
    }

    /**
     * testIsEmptyReturnsFalseWhenAtLeastOneViolationExists
     */
    public function testIsEmptyReturnsFalseWhenAtLeastOneViolationExists(): void
    {
        $report = new Report();
        $report->addRuleViolation($this->getRuleViolationMock('foo.txt', 4, 5));

        static::assertFalse($report->isEmpty());
    }

    /**
     * testHasErrorsReturnsFalseByDefault
     *
     * @since 1.2.1
     */
    public function testHasErrorsReturnsFalseByDefault(): void
    {
        $report = new Report();
        static::assertFalse($report->hasErrors());
    }

    /**
     * testHasErrorsReturnsTrueWhenReportContainsAtLeastOneError
     *
     * @since 1.2.1
     */
    public function testHasErrorsReturnsTrueWhenReportContainsAtLeastOneError(): void
    {
        $report = new Report();
        $report->addError(new ProcessingError('Failing file "/foo.php".'));

        static::assertTrue($report->hasErrors());
    }

    /**
     * testGetErrorsReturnsEmptyIteratorByDefault
     *
     * @since 1.2.1
     */
    public function testGetErrorsReturnsEmptyIteratorByDefault(): void
    {
        $report = new Report();
        static::assertSame(0, iterator_count($report->getErrors()));
    }

    /**
     * testGetErrorsReturnsPreviousAddedProcessingError
     *
     * @since 1.2.1
     */
    public function testGetErrorsReturnsPreviousAddedProcessingError(): void
    {
        $report = new Report();
        $report->addError(new ProcessingError('Failing file "/foo.php".'));

        static::assertSame(1, iterator_count($report->getErrors()));
    }

    public function testReportShouldIgnoreBaselineViolation(): void
    {
        /** @var RuleViolation $ruleA */
        $ruleA = $this->getRuleViolationMock('foo.txt');

        /** @var RuleViolation $ruleB */
        $ruleB = $this->getRuleViolationMock('bar.txt', 1, 2);

        // setup baseline
        $violation = new ViolationBaseline($ruleA->getRule()::class, 'foo.txt', null);
        $baseline = new BaselineSet();
        $baseline->addEntry($violation);

        // setup report
        $report = new Report(new BaselineValidator($baseline, BaselineMode::None));
        $report->addRuleViolation($ruleA);
        $report->addRuleViolation($ruleB);

        // only expect ruleB
        $violations = $report->getRuleViolations();
        static::assertCount(1, $violations);
        static::assertSame($ruleB, $violations[0]);
    }

    public function testReportShouldIgnoreNewViolationsOnBaselineUpdate(): void
    {
        /** @var RuleViolation $ruleA */
        $ruleA = $this->getRuleViolationMock('foo.txt');

        /** @var RuleViolation $ruleB */
        $ruleB = $this->getRuleViolationMock('bar.txt', 1, 2);

        // setup baseline
        $violation = new ViolationBaseline($ruleA->getRule()::class, 'foo.txt', null);
        $baseline = new BaselineSet();
        $baseline->addEntry($violation);

        // setup report
        $report = new Report(new BaselineValidator($baseline, BaselineMode::Update));
        $report->addRuleViolation($ruleA);
        $report->addRuleViolation($ruleB);

        // only expect ruleA, as ruleB is new and should not be in the report.
        $violations = $report->getRuleViolations();
        static::assertCount(1, $violations);
        static::assertSame($ruleA, $violations[0]);
    }
}
