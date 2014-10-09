<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PHPMD\Renderer;

use PHPMD\AbstractTest;
use PHPMD\ProcessingError;
use PHPMD\Stubs\WriterStub;

/**
 * Test case for the text renderer implementation.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
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
            PHP_EOL .
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
            PHP_EOL .
            "/tmp/foo.php\t-\tFailed for file \"/tmp/foo.php\"." . PHP_EOL .
            "/tmp/bar.php\t-\tFailed for file \"/tmp/bar.php\"." . PHP_EOL .
            "/tmp/baz.php\t-\tFailed for file \"/tmp/baz.php\"." . PHP_EOL,
            $writer->getData()
        );
    }
}
