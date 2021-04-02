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

namespace PHPMD;

use PHPMD\Renderer\XMLRenderer;
use PHPMD\Stubs\WriterStub;

/**
 * Test case for the main PHPMD class.
 *
 * @covers \PHPMD\PHPMD
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
     * testHasErrorsReturnsFalseByDefault
     *
     * @return void
     */
    public function testHasErrorsReturnsFalseByDefault()
    {
        $phpmd = new PHPMD();
        $this->assertFalse($phpmd->hasErrors());
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

        $this->assertFalse($phpmd->hasErrors());
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

        $this->assertFalse($phpmd->hasErrors());
        $this->assertTrue($phpmd->hasViolations());
    }

    /**
     * @return void
     */
    public function testHasViolationsReturnsFalseWhenViolationIsBaselined()
    {
        self::changeWorkingDirectory();

        $baselineSet = $this->getMockBuilder('\PHPMD\Baseline\BaselineSet')
            ->disableOriginalConstructor()
            ->getMock();
        $baselineSet->expects(static::exactly(2))->method('contains')->willReturn(true);

        $renderer = new XMLRenderer();
        $renderer->setWriter(new WriterStub());

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/source_with_npath_violation.php'),
            'pmd-refset1',
            array($renderer),
            new RuleSetFactory(),
            $baselineSet
        );

        static::assertFalse($phpmd->hasViolations());
    }

    /**
     * testHasErrorsReturnsTrueForSourceWithError
     *
     * @return void
     */
    public function testHasErrorsReturnsTrueForSourceWithError()
    {
        self::changeWorkingDirectory();

        $renderer = new XMLRenderer();
        $renderer->setWriter(new WriterStub());

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/source_with_parse_error.php'),
            'pmd-refset1',
            array($renderer),
            new RuleSetFactory()
        );

        $this->assertTrue($phpmd->hasErrors());
        $this->assertFalse($phpmd->hasViolations());
    }

    /**
     * testIgnorePattern
     *
     * @return void
     */
    public function testIgnorePattern()
    {
        self::changeWorkingDirectory();

        $phpmd = new PHPMD();

        // Process without exclusions, should result in violations.
        $phpmd->processFiles(
            self::createFileUri('sourceExcluded/'),
            'pmd-refset1',
            array(),
            new RuleSetFactory()
        );

        $this->assertFalse($phpmd->hasErrors());
        $this->assertTrue($phpmd->hasViolations());

        // Process with exclusions, should result in no violations.
        $phpmd->processFiles(
            self::createFileUri('sourceExcluded/'),
            'exclude-pattern',
            array(),
            new RuleSetFactory()
        );

        $this->assertFalse($phpmd->hasErrors());
        $this->assertFalse($phpmd->hasViolations());
    }
}
