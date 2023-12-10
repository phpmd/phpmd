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

use PHPMD\AbstractTestCase;
use PHPMD\ProcessingError;
use PHPMD\Stubs\WriterStub;

/**
 * Test case for the xml renderer implementation.
 *
 * @covers \PHPMD\Renderer\XMLRenderer
 */
class XMLRendererTest extends AbstractTestCase
{
    /**
     * testRendererCreatesExpectedNumberOfXmlElements
     *
     * @return void
     */
    public function testRendererCreatesExpectedNumberOfXmlElements()
    {
        // Create a writer instance.
        $writer = new WriterStub();

        $violations = array(
            $this->getRuleViolationMock('/bar.php'),
            $this->getRuleViolationMock('/foo.php'),
            $this->getRuleViolationMock('/foo.php', 23, 42, null, 'foo <?php bar'),
        );

        $report = $this->getReportWithNoViolation();
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new \ArrayIterator($violations)));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new \ArrayIterator(array())));

        $renderer = new XMLRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertXmlEquals(
            $writer->getData(),
            'renderer/xml_renderer_expected1.xml'
        );
    }

    /**
     * testRendererAddsProcessingErrorsToXmlReport
     *
     * @return void
     * @since 1.2.1
     */
    public function testRendererAddsProcessingErrorsToXmlReport()
    {
        // Create a writer instance.
        $writer = new WriterStub();

        $processingErrors = array(
            new ProcessingError('Failed for file "/tmp/foo.php".'),
            new ProcessingError('Failed for file "/tmp/bar.php".'),
            new ProcessingError('Failed for file "/tmp/baz.php".'),
        );

        $report = $this->getReportWithNoViolation();
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new \ArrayIterator(array())));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new \ArrayIterator($processingErrors)));

        $renderer = new XMLRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertXmlEquals(
            $writer->getData(),
            'renderer/xml_renderer_processing_errors.xml'
        );
    }
}
