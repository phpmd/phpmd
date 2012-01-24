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
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://phpmd.org
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once 'PHP/PMD.php';
require_once 'PHP/PMD/RuleSetFactory.php';
require_once 'PHP/PMD/Renderer/XMLRenderer.php';

/**
 * Test case for the main PHP_PMD class.
 *
 * @category  PHP
 * @package   PHP_PMD
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://phpmd.org
 *
 * @covers PHP_PMD
 * @group phpmd
 * @group unittest
 */
class PHP_PMD_PMDTest extends PHP_PMD_AbstractTest
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
     * Tests the main PHP_PMD interface with default settings an a xml-renderer.
     *
     * @return void
     */
    public function testRunWithDefaultSettingsAndXmlRenderer()
    {
        self::changeWorkingDirectory();

        $writer = new PHP_PMD_Stubs_WriterStub();

        $renderer = new PHP_PMD_Renderer_XMLRenderer();
        $renderer->setWriter($writer);

        $phpmd = new PHP_PMD();
        $phpmd->processFiles(
            self::createFileUri('source/ccn_function.php'),
            'pmd-refset1',
            array($renderer),
            new PHP_PMD_RuleSetFactory()
        );

        $this->assertXmlEquals($writer->getData(), 'pmd/default-xml.xml');
    }

    /**
     * testRunWithDefaultSettingsAndXmlRendererAgainstSingleFile
     *
     * @return void
     */
    public function testRunWithDefaultSettingsAndXmlRendererAgainstDirectory()
    {
        self::changeWorkingDirectory();

        $writer = new PHP_PMD_Stubs_WriterStub();

        $renderer = new PHP_PMD_Renderer_XMLRenderer();
        $renderer->setWriter($writer);

        $phpmd = new PHP_PMD();
        $phpmd->processFiles(
            self::createFileUri('source'),
            'pmd-refset1',
            array($renderer),
            new PHP_PMD_RuleSetFactory()
        );

        $this->assertXmlEquals($writer->getData(), 'pmd/single-directory.xml');
    }

    /**
     * testRunWithDefaultSettingsAndXmlRendererAgainstSingleFile
     *
     * @return void
     */
    public function testRunWithDefaultSettingsAndXmlRendererAgainstSingleFile()
    {
        self::changeWorkingDirectory();

        $writer = new PHP_PMD_Stubs_WriterStub();

        $renderer = new PHP_PMD_Renderer_XMLRenderer();
        $renderer->setWriter($writer);

        $phpmd = new PHP_PMD();
        $phpmd->processFiles(
            self::createFileUri('source/ccn_function.php'),
            'pmd-refset1',
            array($renderer),
            new PHP_PMD_RuleSetFactory()
        );

        $this->assertXmlEquals($writer->getData(), 'pmd/single-file.xml');
    }

    /**
     * testHasViolationsReturnsFalseByDefault
     *
     * @return void
     */
    public function testHasViolationsReturnsFalseByDefault()
    {
        $phpmd = new PHP_PMD();
        $this->assertFalse($phpmd->hasViolations());
    }

    /**
     * testHasViolationsReturnsFalseForSourceWithoutViolations
     *
     * @return void
     */
    public function testHasViolationsReturnsFalseForSourceWithoutViolations()
    {
        self::changeWorkingDirectory();

        $renderer = new PHP_PMD_Renderer_XMLRenderer();
        $renderer->setWriter(new PHP_PMD_Stubs_WriterStub());

        $phpmd = new PHP_PMD();
        $phpmd->processFiles(
            self::createFileUri('source/source_without_violations.php'),
            'pmd-refset1',
            array($renderer),
            new PHP_PMD_RuleSetFactory()
        );

        $this->assertFalse($phpmd->hasViolations());
    }

    /**
     * testHasViolationsReturnsTrueForSourceWithViolation
     *
     * @return void
     */
    public function testHasViolationsReturnsTrueForSourceWithViolation()
    {
        self::changeWorkingDirectory();

        $renderer = new PHP_PMD_Renderer_XMLRenderer();
        $renderer->setWriter(new PHP_PMD_Stubs_WriterStub());

        $phpmd = new PHP_PMD();
        $phpmd->processFiles(
            self::createFileUri('source/source_with_npath_violation.php'),
            'pmd-refset1',
            array($renderer),
            new PHP_PMD_RuleSetFactory()
        );

        $this->assertTrue($phpmd->hasViolations());
    }
}
