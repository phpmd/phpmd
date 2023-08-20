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
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD\Renderer;

use ArrayIterator;
use PHPMD\AbstractTest;
use PHPMD\Console\OutputInterface;
use PHPMD\ProcessingError;
use PHPMD\Stubs\RuleStub;
use PHPMD\Stubs\WriterStub;

/**
 * Test case for the text renderer implementation.
 *
 * @covers \PHPMD\Renderer\TextRenderer
 */
class TextRendererTest extends AbstractTest
{
    /**
     * testRendererCreatesExpectedNumberOfTextEntries
     *
     * @return void
     */
    public function testRendererCreatesExpectedNumberOfTextEntries()
    {
        // Create a writer instance.
        $writer = new WriterStub();
        $rule = new RuleStub();
        $rule->setName('LongerNamedRule');
        $rule->setDescription('An other description for this rule');

        $violations = array(
            $this->getRuleViolationMock('/bar.php', 1, 42, $rule, $rule->getDescription()),
            $this->getRuleViolationMock('/foo-biz.php', 2),
            $this->getRuleViolationMock('/foo.php', 34),
        );

        $report = $this->getReportWithNoViolation();
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new ArrayIterator($violations)));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new ArrayIterator(array())));

        $renderer = new TextRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertEquals(
            "/bar.php:1      LongerNamedRule  An other description for this rule" . PHP_EOL .
            "/foo-biz.php:2  RuleStub         Test description" . PHP_EOL .
            "/foo.php:34     RuleStub         Test description" . PHP_EOL,
            $writer->getData()
        );
    }

    /**
     * @return void
     */
    public function testRendererSupportVerbose()
    {
        // Create a writer instance.
        $writer = new WriterStub();
        $rule = new RuleStub();
        $rule->setName('LongerNamedRule');
        $rule->setDescription('An other description for this rule');

        $renderer = new TextRenderer();
        $renderer->setWriter($writer);
        $renderer->setVerbosityLevel(OutputInterface::VERBOSITY_VERBOSE);

        $violations = array(
            $this->getRuleViolationMock('/bar.php', 1, 42, $rule, $rule->getDescription()),
        );

        $report = $this->getReportWithNoViolation();
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new ArrayIterator($violations)));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new ArrayIterator(array())));

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertEquals(
            'LongerNamedRule  An other description for this rule' . PHP_EOL .
            '📁 in /bar.php on line 1' . PHP_EOL .
            '🔗 testruleset.xml https://phpmd.org/rules/testruleset.html#longernamedrule' . PHP_EOL . PHP_EOL,
            $writer->getData()
        );
    }

    /**
     * @return void
     */
    public function testRendererSupportColor()
    {
        // Create a writer instance.
        $writer = new WriterStub();
        $rule = new RuleStub();
        $rule->setName('LongerNamedRule');
        $rule->setDescription('An other description for this rule');

        $renderer = new TextRenderer();
        $renderer->setWriter($writer);
        $renderer->setColored(true);

        $violations = array(
            $this->getRuleViolationMock('/bar.php', 1, 42, $rule, $rule->getDescription()),
        );

        $report = $this->getReportWithNoViolation();
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new ArrayIterator($violations)));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new ArrayIterator(array())));

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertEquals(
            "/bar.php:1  \033[33mLongerNamedRule\033[0m  \033[31mAn other description for this rule\033[0m" . PHP_EOL,
            $writer->getData()
        );
    }

    /**
     * testRendererAddsProcessingErrorsToTextReport
     *
     * @return void
     */
    public function testRendererAddsProcessingErrorsToTextReport()
    {
        // Create a writer instance.
        $writer = new WriterStub();

        $errors = array(
            new ProcessingError('Failed for file "/tmp/foo.php".'),
            new ProcessingError('Failed for file "/tmp/bar.php".'),
            new ProcessingError('Failed for file "/tmp/baz.php".'),
        );

        $report = $this->getReportWithNoViolation();
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new ArrayIterator(array())));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new ArrayIterator($errors)));

        $renderer = new TextRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertEquals(
            "/tmp/foo.php\t-\tFailed for file \"/tmp/foo.php\"." . PHP_EOL .
            "/tmp/bar.php\t-\tFailed for file \"/tmp/bar.php\"." . PHP_EOL .
            "/tmp/baz.php\t-\tFailed for file \"/tmp/baz.php\"." . PHP_EOL,
            $writer->getData()
        );
    }
}
