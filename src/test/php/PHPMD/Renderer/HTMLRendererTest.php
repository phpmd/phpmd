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
 * Test case for the html renderer implementation.
 *
 * @covers \PHPMD\Renderer\HTMLRenderer
 * @group phpmd
 * @group phpmd::renderer
 * @group unittest
 */
class HTMLRendererTest extends AbstractTest
{
    /**
     * testRendererCreatesExpectedNumberOfTextEntries
     *
     * @return void
     */
    public function testRendererCreatesExpectedHtmlTableRow()
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

        $renderer = new HTMLRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertContains(
            '<tr>' . PHP_EOL .
            '<td align="center">2</td>' . PHP_EOL .
            '<td>/foo.php</td>' . PHP_EOL .
            '<td align="center" width="5%">2</td>' . PHP_EOL .
            '<td><a href="https://phpmd.org/rules/index.html">Test description</a></td>' . PHP_EOL .
            '</tr>',
            $writer->getData()
        );
    }

    /**
     * testRendererAddsProcessingErrorsToHtmlReport
     *
     * @return void
     */
    public function testRendererAddsProcessingErrorsToHtmlReport()
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

        $renderer = new HTMLRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertContains(
            '<tr>' .
            '<td>/tmp/bar.php</td>' .
            '<td>Failed for file &quot;/tmp/bar.php&quot;.</td>' .
            '</tr>',
            $writer->getData()
        );
    }
}
