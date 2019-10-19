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
 * Test case for the JSON renderer implementation.
 *
 * @covers \PHPMD\Renderer\JSONRenderer
 */
class JSONRendererTest extends AbstractTest
{
    /**
     * testRendererCreatesExpectedNumberOfJsonElements
     *
     * @return void
     */
    public function testRendererCreatesExpectedNumberOfJsonElements()
    {
        $writer = new WriterStub();
        
        $violations = array(
            $this->getRuleViolationMock('/bar.php'),
            $this->getRuleViolationMock('/foo.php'),
            $this->getRuleViolationMock('/bar.php'), // TODO Set with description "foo <?php bar".
        );

        $report = $this->getReportMock(0);
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new \ArrayIterator($violations)));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new \ArrayIterator(array())));

        $renderer = new JSONRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertJsonEquals(
            $writer->getData(),
            'renderer/json_renderer_expected.json'
        );
    }

    /**
     * testRendererAddsProcessingErrorsToJsonReport
     *
     * @return void
     */
    public function testRendererAddsProcessingErrorsToJsonReport()
    {
        $writer = new WriterStub();

        $processingErrors = array(
            new ProcessingError('Failed for file "/tmp/foo.php".'),
            new ProcessingError('Failed for file "/tmp/bar.php".'),
            new ProcessingError('Failed for file "/tmp/baz.php".'),
            new ProcessingError('Cannot read file "/tmp/foo.php". Permission denied.'),
        );

        $report = $this->getReportMock(0);
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new \ArrayIterator(array())));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new \ArrayIterator($processingErrors)));

        $renderer = new JSONRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertJsonEquals(
            $writer->getData(),
            'renderer/json_renderer_processing_errors.json'
        );
    }
}
