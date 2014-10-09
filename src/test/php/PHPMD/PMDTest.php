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

namespace PHPMD;

use PHPMD\Renderer\XMLRenderer;
use PHPMD\Stubs\WriterStub;

/**
 * Test case for the main PHPMD class.
 *
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright 2008-2014 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PHPMD\PHPMD
 * @group phpmd
 * @group unittest
 */
class PHPMDTest extends AbstractTest
{
    /**
     * Tests the main PHPMD interface with default settings an a xml-renderer.
     *
     * @return void
     */
    public function testRunWithDefaultSettingsAndXmlRenderer()
    {
        self::changeWorkingDirectory();

        $writer = new WriterStub();

        $renderer = new XMLRenderer();
        $renderer->setWriter($writer);

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/ccn_function.php'),
            'pmd-refset1',
            array($renderer),
            new RuleSetFactory()
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

        $writer = new WriterStub();

        $renderer = new XMLRenderer();
        $renderer->setWriter($writer);

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source'),
            'pmd-refset1',
            array($renderer),
            new RuleSetFactory()
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

        $writer = new WriterStub();

        $renderer = new XMLRenderer();
        $renderer->setWriter($writer);

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/ccn_function.php'),
            'pmd-refset1',
            array($renderer),
            new RuleSetFactory()
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
        $phpmd = new PHPMD();
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

        $renderer = new XMLRenderer();
        $renderer->setWriter(new WriterStub());

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/source_without_violations.php'),
            'pmd-refset1',
            array($renderer),
            new RuleSetFactory()
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

        $renderer = new XMLRenderer();
        $renderer->setWriter(new WriterStub());

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/source_with_npath_violation.php'),
            'pmd-refset1',
            array($renderer),
            new RuleSetFactory()
        );

        $this->assertTrue($phpmd->hasViolations());
    }
}
