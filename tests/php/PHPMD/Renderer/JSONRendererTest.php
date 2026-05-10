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
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test case for the JSON renderer implementation.
 */
#[CoversClass(JSONRenderer::class)]
class JSONRendererTest extends AbstractTestCase
{
    /**
     * testRendererCreatesExpectedNumberOfJsonElements
     */
    public function testRendererCreatesExpectedNumberOfJsonElements(): void
    {
        $writer = new BufferedOutput();

        $violations = [
            $this->getRuleViolationMock('/bar.php'),
            $this->getRuleViolationMock('/foo.php'),
            $this->getRuleViolationMock('/bar.php'), // TODO Set with description "foo <?php bar".
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator($violations));
        $report->expects(static::once())
            ->method('getErrors')
            ->willReturn(new ArrayIterator([]));

        $renderer = new JSONRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertJsonEquals(
            $writer->fetch(),
            'renderer/json_renderer_expected.json'
        );
    }

    /**
     * testRendererAddsProcessingErrorsToJsonReport
     */
    public function testRendererAddsProcessingErrorsToJsonReport(): void
    {
        $writer = new BufferedOutput();

        $processingErrors = [
            new ProcessingError('Failed for file "/tmp/foo.php".'),
            new ProcessingError('Failed for file "/tmp/bar.php".'),
            new ProcessingError('Failed for file "/tmp/baz.php".'),
            new ProcessingError('Cannot read file "/tmp/foo.php". Permission denied.'),
        ];

        $report = $this->getReportWithNoViolation();
        $report->expects(static::once())
            ->method('getRuleViolations')
            ->willReturn(new ArrayIterator([]));
        $report->expects(static::once())
            ->method('getErrors')
            ->willReturn(new ArrayIterator($processingErrors));

        $renderer = new JSONRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertJsonEquals(
            $writer->fetch(),
            'renderer/json_renderer_processing_errors.json'
        );
    }
}
