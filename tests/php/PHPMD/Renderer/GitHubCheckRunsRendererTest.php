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
use PHPMD\Stubs\RuleStub;
use PHPMD\TextUI\Command;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Output\BufferedOutput;

#[CoversClass(GitHubCheckRunsRenderer::class)]
class GitHubCheckRunsRendererTest extends AbstractTestCase
{
    public function testRendererOutputsNoMessDetectedWhenNoViolations(): void
    {
        $writer = new BufferedOutput();

        $report = $this->getReportWithNoViolation();
        $report->method('getRuleViolations')
            ->willReturn(new ArrayIterator([]));
        $report->method('getErrors')
            ->willReturn(new ArrayIterator([]));

        $renderer = new GitHubCheckRunsRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $output = $writer->fetch();

        /** @var array{title: string, summary: string, annotations: list<mixed>} $data */
        $data = json_decode($output, true);
        static::assertStringContainsString('phpmd', $data['title']);
        static::assertSame('No mess detected', $data['summary']);
        static::assertSame([], $data['annotations']);
    }

    public function testRendererOutputsViolationsAsAnnotations(): void
    {
        $writer = new BufferedOutput();

        $violations = [
            $this->getRuleViolationMock('/bar.php', 1, 5),
            $this->getRuleViolationMock('/foo.php', 10, 20),
        ];

        $report = $this->getReportWithNoViolation();
        $report->method('getRuleViolations')
            ->willReturnCallback(fn() => new ArrayIterator($violations));
        $report->method('getErrors')
            ->willReturnCallback(fn() => new ArrayIterator([]));
        $report->method('getElapsedTimeInMillis')
            ->willReturn(100.0);

        $renderer = new GitHubCheckRunsRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $output = $writer->fetch();

        /** @var array{title: string, summary: string, annotations: list<array{path: string, violations: list<array{start_line: int, end_line: int, annotation_level: string, message: string, title: string, raw_details: array<string, mixed>}>}>} $data */
        $data = json_decode($output, true);

        static::assertSame(sprintf('phpmd %s', Command::getVersion()), $data['title']);
        static::assertCount(2, $data['annotations']);

        $firstAnnotation = $data['annotations'][0];
        static::assertSame('/bar.php', $firstAnnotation['path']);
        $firstViolation = $firstAnnotation['violations'][0];
        static::assertSame(1, $firstViolation['start_line']);
        static::assertSame(5, $firstViolation['end_line']);
        static::assertSame('notice', $firstViolation['annotation_level']);
        static::assertSame('Test description', $firstViolation['message']);
        static::assertSame('RuleStub', $firstViolation['title']);
        static::assertSame('TestRuleSet', $firstViolation['raw_details']['ruleSet']);
        static::assertSame('https://phpmd.org/rules/index.html', $firstViolation['raw_details']['externalInfoUrl']);

        $secondAnnotation = $data['annotations'][1];
        static::assertSame('/foo.php', $secondAnnotation['path']);
        static::assertSame(10, $secondAnnotation['violations'][0]['start_line']);
        static::assertSame(20, $secondAnnotation['violations'][0]['end_line']);
    }

    public function testRendererMapsAnnotationLevelsFromPriority(): void
    {
        $writer = new BufferedOutput();

        $rule = new RuleStub();
        $rule->setPriority(1);

        $violations = [
            $this->getRuleViolationMock('/baz.php', 5, 10, $rule),
        ];

        $report = $this->getReportWithNoViolation();
        $report->method('getRuleViolations')
            ->willReturnCallback(fn() => new ArrayIterator($violations));
        $report->method('getErrors')
            ->willReturnCallback(fn() => new ArrayIterator([]));
        $report->method('getElapsedTimeInMillis')
            ->willReturn(50.0);

        $renderer = new GitHubCheckRunsRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $output = $writer->fetch();

        /** @var array{annotations: list<array{violations: list<array{annotation_level: string, raw_details: array{priority: int}}>}>} $data */
        $data = json_decode($output, true);

        $violation = $data['annotations'][0]['violations'][0];
        static::assertSame('failure', $violation['annotation_level']);
        static::assertSame(1, $violation['raw_details']['priority']);
    }
}
