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

use PHPMD\AbstractTest;
use PHPMD\ProcessingError;
use PHPMD\Stubs\WriterStub;

/**
 * Test case for the text renderer implementation.
 *
 * @covers \PHPMD\Renderer\TextRenderer
 * @group phpmd
 * @group phpmd::renderer
 * @group unittest
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

        $violations = array(
            $this->getRuleViolationMock('/bar.php', 1),
            $this->getRuleViolationMock('/foo.php', 2),
            $this->getRuleViolationMock('/foo.php', 3),
        );

        $report = $this->getReportMock(0);
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new \ArrayIterator($violations)));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new \ArrayIterator(array())));

        $renderer = new TextRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertEquals(
            "/bar.php:1\tTest description" . PHP_EOL .
            "/foo.php:2\tTest description" . PHP_EOL .
            "/foo.php:3\tTest description" . PHP_EOL,
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

        $report = $this->getReportMock(0);
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new \ArrayIterator(array())));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new \ArrayIterator($errors)));

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
