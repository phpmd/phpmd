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
use PHPMD\AbstractTestCase;
use PHPMD\ProcessingError;
use PHPMD\Stubs\RuleStub;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test case for the text renderer implementation.
 */
#[CoversClass(TextRenderer::class)]
class TextRendererTest extends AbstractTestCase
{
    public function testRendererCreatesExpectedNumberOfTextEntries(): void
    {
        // Create a writer instance.
        $writer = new BufferedOutput();
        $rule = new RuleStub();
        $rule->setName('LongerNamedRule');
        $rule->setDescription('An other description for this rule');

        $violations = [
            $this->getRuleViolationMock('/bar.php', 1, 42, $rule, $rule->getDescription()),
            $this->getRuleViolationMock('/foo-biz.php', 2),
            $this->getRuleViolationMock('/foo.php', 34),
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator($violations));
        $report->expects(static::once())
            ->method('getErrors')
            ->willReturn(new ArrayIterator([]));

        $renderer = new TextRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        static::assertEquals(
            '/bar.php:1      LongerNamedRule  An other description for this rule' . PHP_EOL .
            '/foo-biz.php:2  RuleStub         Test description' . PHP_EOL .
            '/foo.php:34     RuleStub         Test description' . PHP_EOL,
            $writer->fetch()
        );
    }

    public function testRendererSupportVerbose(): void
    {
        // Create a writer instance.
        $writer = new BufferedOutput();
        $rule = new RuleStub();
        $rule->setName('LongerNamedRule');
        $rule->setDescription('An other description for this rule');

        $renderer = new TextRenderer();
        $renderer->setWriter($writer);
        $renderer->setVerbosityLevel(OutputInterface::VERBOSITY_VERBOSE);

        $violations = [
            $this->getRuleViolationMock('/bar.php', 1, 42, $rule, $rule->getDescription()),
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator($violations));
        $report->expects(static::once())
            ->method('getErrors')
            ->willReturn(new ArrayIterator([]));

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        static::assertEquals(
            'LongerNamedRule  An other description for this rule' . PHP_EOL .
            '📁 in /bar.php on line 1' . PHP_EOL .
            '🔗 testruleset.xml https://phpmd.org/rules/testruleset.html#longernamedrule' . PHP_EOL . PHP_EOL,
            $writer->fetch()
        );
    }

    public function testRendererSupportColor(): void
    {
        // Create a writer instance.
        $writer = new BufferedOutput();
        $rule = new RuleStub();
        $rule->setName('LongerNamedRule');
        $rule->setDescription('An other description for this rule');

        $renderer = new TextRenderer();
        $renderer->setWriter($writer);
        $renderer->setColored(true);

        $violations = [
            $this->getRuleViolationMock('/bar.php', 1, 42, $rule, $rule->getDescription()),
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator($violations));
        $report->expects(static::once())
            ->method('getErrors')
            ->willReturn(new ArrayIterator([]));

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        static::assertEquals(
            "/bar.php:1  \033[33mLongerNamedRule\033[0m  \033[31mAn other description for this rule\033[0m" . PHP_EOL,
            $writer->fetch()
        );
    }

    public function testRendererAddsProcessingErrorsToTextReport(): void
    {
        // Create a writer instance.
        $writer = new BufferedOutput();

        $errors = [
            new ProcessingError('Failed for file "/tmp/foo.php".'),
            new ProcessingError('Failed for file "/tmp/bar.php".'),
            new ProcessingError('Failed for file "/tmp/baz.php".'),
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator([]));
        $report->expects(static::once())
            ->method('getErrors')
            ->willReturn(new ArrayIterator($errors));

        $renderer = new TextRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        static::assertEquals(
            "/tmp/foo.php\t-\tFailed for file \"/tmp/foo.php\"." . PHP_EOL .
            "/tmp/bar.php\t-\tFailed for file \"/tmp/bar.php\"." . PHP_EOL .
            "/tmp/baz.php\t-\tFailed for file \"/tmp/baz.php\"." . PHP_EOL,
            $writer->fetch()
        );
    }
}
