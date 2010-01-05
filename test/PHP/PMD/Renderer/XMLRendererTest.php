<?php
/**
 * This file is part of PHP_PMD.
 *
 * PHP Version 5
 *
<<<<<<< HEAD
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@pdepend.org>.
=======
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@phpmd.org>.
>>>>>>> 0.2.x
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
<<<<<<< HEAD
 * @author     Manuel Pichler <mapi@pdepend.org>
=======
 * @author     Manuel Pichler <mapi@phpmd.org>
>>>>>>> 0.2.x
 * @copyright  2009-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://phpmd.org
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/PMD/Renderer/XMLRenderer.php';

/**
 * Test case for the xml renderer implementation.
 *
 * @category   PHP
 * @package    PHP_PMD
 * @subpackage Renderer
<<<<<<< HEAD
 * @author     Manuel Pichler <mapi@pdepend.org>
=======
 * @author     Manuel Pichler <mapi@phpmd.org>
>>>>>>> 0.2.x
 * @copyright  2009-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://phpmd.org
 */
class PHP_PMD_Renderer_XMLRendererTest extends PHP_PMD_AbstractTest
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
     * testRendererCreatesExpectedNumberOfXmlElements
     *
     * @return void
     * @covers PHP_PMD_Renderer_XMLRenderer
     * @group phpmd
     * @group phpmd::renderer
     * @group unittest
     */
    public function testRendererCreatesExpectedNumberOfXmlElements()
    {
        // Create a writer instance.
        $writer = new PHP_PMD_Stubs_WriterStub();
        
        $violations = array(
            $this->getRuleViolationMock('/bar.php'),
            $this->getRuleViolationMock('/foo.php'),
            $this->getRuleViolationMock('/foo.php'),
        );

        $report = $this->getReportMock(0);
        $report->expects($this->once())
               ->method('getRuleViolations')
               ->will($this->returnValue(new ArrayIterator($violations)));

        $renderer = new PHP_PMD_Renderer_XMLRenderer();
        $renderer->setWriter($writer);

        $renderer->start();
        $renderer->renderReport($report);
        $renderer->end();

        $this->assertXmlEquals(
            $writer->getData(),
            'renderer/xml_renderer_expected1.xml'
        );
    }
}