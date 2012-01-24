<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
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
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Renderer
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/PMD/ProcessingError.php';
require_once 'PHP/PMD/Renderer/HTMLRenderer.php';

/**
 * Test case for the html renderer implementation.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Renderer
 * @author     Manuel Pichler <mapi@phpmd.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 *
 * @covers PHP_PMD_Renderer_HTMLRenderer
 * @group phpmd
 * @group phpmd::renderer
 * @group unittest
 */
class PHP_PMD_Renderer_HTMLRendererTest extends PHP_PMD_AbstractTest
{
    /**
     * Includes the write stub class.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        // Import writer stub
        include_once self::createFileUri('stubs/WriterStub.php');
    }

    /**
     * testRendererCreatesExpectedNumberOfTextEntries
     *
     * @return void
     */
    public function testRendererCreatesExpectedHtmlTableRow()
    {
        // Create a writer instance.
        $writer = new PHP_PMD_Stubs_WriterStub();

        $violations = array(
            $this->getRuleViolationMock('/bar.php', 1),
            $this->getRuleViolationMock('/foo.php', 2),
            $this->getRuleViolationMock('/foo.php', 3),
        );

        $report = $this->getReportMock(0);
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new ArrayIterator($violations)));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new ArrayIterator(array())));

        $renderer = new PHP_PMD_Renderer_HTMLRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertContains(
            '<tr>' . PHP_EOL .
            '<td align="center">2</td>' . PHP_EOL .
            '<td>/foo.php</td>' . PHP_EOL .
            '<td align="center" width="5%">2</td>' . PHP_EOL .
            '<td><a href="http://phpmd.org/rules/index.html">Test description</a></td>' . PHP_EOL .
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
        $writer = new PHP_PMD_Stubs_WriterStub();

        $errors = array(
            new PHP_PMD_ProcessingError('Failed for file "/tmp/foo.php".'),
            new PHP_PMD_ProcessingError('Failed for file "/tmp/bar.php".'),
            new PHP_PMD_ProcessingError('Failed for file "/tmp/baz.php".'),
        );

        $report = $this->getReportMock(0);
        $report->expects($this->once())
            ->method('getRuleViolations')
            ->will($this->returnValue(new ArrayIterator(array())));
        $report->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(new ArrayIterator($errors)));

        $renderer = new PHP_PMD_Renderer_HTMLRenderer();
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
