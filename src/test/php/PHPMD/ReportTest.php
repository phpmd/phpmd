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
     *
     * @return void
     */
    public function testReportReturnsAListWithAllRuleViolations()
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

        $this->assertSame($expected, $actual);
    }

    /**
     * Tests that the report returns the result by the violation line number.
     *
     * @return void
     */
    public function testReportSortsResultByLineNumber()
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

        $this->assertSame($expected, $actual);
    }

    /**
     * Tests that the timer method returns the expected result.
     *
     * @return void
     */
    public function testReportTimerReturnsMilliSeconds()
    {
        $start = microtime(true);

        $report = new Report();
        $report->start();
        usleep(50000); // 50 Milli Seconds
        $report->end();

        $time = ceil((microtime(true) - $start) * 1000.0);

        // Windows does not compute the time correctly, simply skipping
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $this->assertGreaterThanOrEqual(50, $report->getElapsedTimeInMillis());
        }
        $this->assertLessThanOrEqual($time, $report->getElapsedTimeInMillis());
    }

    /**
     * testIsEmptyReturnsTrueByDefault
     *
     * @return void
     */
    public function testIsEmptyReturnsTrueByDefault()
    {
        $report = new Report();
        $this->assertTrue($report->isEmpty());
    }

    /**
     * testIsEmptyReturnsFalseWhenAtLeastOneViolationExists
     *
     * @return void
     */
    public function testIsEmptyReturnsFalseWhenAtLeastOneViolationExists()
    {
        $report = new Report();
        $report->addRuleViolation($this->getRuleViolationMock('foo.txt', 4, 5));

        $this->assertFalse($report->isEmpty());
    }

    /**
     * testHasErrorsReturnsFalseByDefault
     *
     * @return void
     * @since 1.2.1
     */
    public function testHasErrorsReturnsFalseByDefault()
    {
        $report = new Report();
        $this->assertFalse($report->hasErrors());
    }

    /**
     * testHasErrorsReturnsTrueWhenReportContainsAtLeastOneError
     *
     * @return void
     * @since 1.2.1
     */
    public function testHasErrorsReturnsTrueWhenReportContainsAtLeastOneError()
    {
        $report = new Report();
        $report->addError(new ProcessingError('Failing file "/foo.php".'));

        $this->assertTrue($report->hasErrors());
    }

    /**
     * testGetErrorsReturnsEmptyIteratorByDefault
     *
     * @return void
     * @since 1.2.1
     */
    public function testGetErrorsReturnsEmptyIteratorByDefault()
    {
        $report = new Report();
        $this->assertSame(0, iterator_count($report->getErrors()));
    }

    /**
     * testGetErrorsReturnsPreviousAddedProcessingError
     *
     * @return void
     * @since 1.2.1
     */
    public function testGetErrorsReturnsPreviousAddedProcessingError()
    {
        $report = new Report();
        $report->addError(new ProcessingError('Failing file "/foo.php".'));

        $this->assertSame(1, iterator_count($report->getErrors()));
    }

    /**
     * @return void
     */
    public function testReportShouldIgnoreBaselineViolation()
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
        $report = new Report(new BaselineValidator($baseline, BaselineMode::NONE));
        $report->addRuleViolation($ruleA);
        $report->addRuleViolation($ruleB);

        // only expect ruleB
        $violations = $report->getRuleViolations();
        static::assertCount(1, $violations);
        static::assertSame($ruleB, $violations[0]);
    }

    /**
     * @return void
     */
    public function testReportShouldIgnoreNewViolationsOnBaselineUpdate()
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
        $report = new Report(new BaselineValidator($baseline, BaselineMode::UPDATE));
        $report->addRuleViolation($ruleA);
        $report->addRuleViolation($ruleB);

        // only expect ruleA, as ruleB is new and should not be in the report.
        $violations = $report->getRuleViolations();
        static::assertCount(1, $violations);
        static::assertSame($ruleA, $violations[0]);
    }
}
