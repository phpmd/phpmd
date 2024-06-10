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
 * @author    Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license   https://opensource.org/licenses/bsd-license.php BSD License
 * @link      http://phpmd.org/
 */

namespace PHPMD;

use PHPMD\Baseline\BaselineMode;
use PHPMD\Baseline\BaselineSet;
use PHPMD\Baseline\BaselineValidator;
use PHPMD\Renderer\XMLRenderer;
use PHPMD\Stubs\WriterStub;
use Throwable;

/**
 * Test case for the main PHPMD class.
 *
 * @covers \PHPMD\PHPMD
 */
class PHPMDTest extends AbstractTestCase
{
    private RuleSetFactory $ruleSetFactory;

    protected function setUp(): void
    {
        $this->ruleSetFactory = new RuleSetFactory();
    }

    /**
     * Tests the main PHPMD interface with default settings an a xml-renderer.
     * @throws Throwable
     */
    public function testRunWithDefaultSettingsAndXmlRenderer(): void
    {
        self::changeWorkingDirectory();

        $writer = new WriterStub();

        $renderer = new XMLRenderer();
        $renderer->setWriter($writer);

        $phpmd = new PHPMD();

        $phpmd->processFiles(
            self::createFileUri('source/ccn_function.php'),
            $this->ruleSetFactory->getIgnorePattern('pmd-refset1'),
            [$renderer],
            $this->ruleSetFactory->createRuleSets('pmd-refset1'),
            new Report()
        );

        $this->assertXmlEquals($writer->getData(), 'pmd/default-xml.xml');
    }

    /**
     * testRunWithDefaultSettingsAndXmlRendererAgainstSingleFile
     * @throws Throwable
     */
    public function testRunWithDefaultSettingsAndXmlRendererAgainstDirectory(): void
    {
        self::changeWorkingDirectory();

        $writer = new WriterStub();

        $renderer = new XMLRenderer();
        $renderer->setWriter($writer);

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source'),
            $this->ruleSetFactory->getIgnorePattern('pmd-refset1'),
            [$renderer],
            $this->ruleSetFactory->createRuleSets('pmd-refset1'),
            new Report()
        );

        $this->assertXmlEquals($writer->getData(), 'pmd/single-directory.xml');
    }

    /**
     * testRunWithDefaultSettingsAndXmlRendererAgainstSingleFile
     * @throws Throwable
     */
    public function testRunWithDefaultSettingsAndXmlRendererAgainstSingleFile(): void
    {
        self::changeWorkingDirectory();

        $writer = new WriterStub();

        $renderer = new XMLRenderer();
        $renderer->setWriter($writer);

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/ccn_function.php'),
            $this->ruleSetFactory->getIgnorePattern('pmd-refset1'),
            [$renderer],
            $this->ruleSetFactory->createRuleSets('pmd-refset1'),
            new Report()
        );

        $this->assertXmlEquals($writer->getData(), 'pmd/single-file.xml');
    }

    /**
     * testHasErrorsReturnsFalseByDefault
     * @throws Throwable
     */
    public function testHasErrorsReturnsFalseByDefault(): void
    {
        $phpmd = new PHPMD();
        static::assertFalse($phpmd->hasErrors());
    }

    /**
     * testHasViolationsReturnsFalseByDefault
     * @throws Throwable
     */
    public function testHasViolationsReturnsFalseByDefault(): void
    {
        $phpmd = new PHPMD();
        static::assertFalse($phpmd->hasViolations());
    }

    /**
     * testHasViolationsReturnsFalseForSourceWithoutViolations
     * @throws Throwable
     */
    public function testHasViolationsReturnsFalseForSourceWithoutViolations(): void
    {
        self::changeWorkingDirectory();

        $renderer = new XMLRenderer();
        $renderer->setWriter(new WriterStub());

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/source_without_violations.php'),
            $this->ruleSetFactory->getIgnorePattern('pmd-refset1'),
            [$renderer],
            $this->ruleSetFactory->createRuleSets('pmd-refset1'),
            new Report()
        );

        static::assertFalse($phpmd->hasErrors());
        static::assertFalse($phpmd->hasViolations());
    }

    /**
     * testHasViolationsReturnsTrueForSourceWithViolation
     * @throws Throwable
     */
    public function testHasViolationsReturnsTrueForSourceWithViolation(): void
    {
        self::changeWorkingDirectory();

        $renderer = new XMLRenderer();
        $renderer->setWriter(new WriterStub());

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/source_with_npath_violation.php'),
            $this->ruleSetFactory->getIgnorePattern('pmd-refset1'),
            [$renderer],
            $this->ruleSetFactory->createRuleSets('pmd-refset1'),
            new Report()
        );

        static::assertFalse($phpmd->hasErrors());
        static::assertTrue($phpmd->hasViolations());
    }

    /**
     * @throws Throwable
     */
    public function testHasViolationsReturnsFalseWhenViolationIsBaselined(): void
    {
        self::changeWorkingDirectory();

        $baselineSet = $this->getMockBuilder(BaselineSet::class)->disableOriginalConstructor()->getMock();
        $baselineSet->expects(static::exactly(2))->method('contains')->willReturn(true);

        $renderer = new XMLRenderer();
        $renderer->setWriter(new WriterStub());

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/source_with_npath_violation.php'),
            $this->ruleSetFactory->getIgnorePattern('pmd-refset1'),
            [$renderer],
            $this->ruleSetFactory->createRuleSets('pmd-refset1'),
            new Report(new BaselineValidator($baselineSet, BaselineMode::None))
        );

        static::assertFalse($phpmd->hasViolations());
    }

    /**
     * testHasErrorsReturnsTrueForSourceWithError
     * @throws Throwable
     */
    public function testHasErrorsReturnsTrueForSourceWithError(): void
    {
        self::changeWorkingDirectory();

        $renderer = new XMLRenderer();
        $renderer->setWriter(new WriterStub());

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/source_with_parse_error.php'),
            $this->ruleSetFactory->getIgnorePattern('pmd-refset1'),
            [$renderer],
            $this->ruleSetFactory->createRuleSets('pmd-refset1'),
            new Report()
        );

        static::assertTrue($phpmd->hasErrors());
        static::assertFalse($phpmd->hasViolations());
    }

    /**
     * testIgnorePattern
     * @throws Throwable
     */
    public function testIgnorePattern(): void
    {
        self::changeWorkingDirectory();

        $phpmd = new PHPMD();

        // Process without exclusions, should result in violations.
        $phpmd->processFiles(
            self::createFileUri('sourceExcluded/'),
            $this->ruleSetFactory->getIgnorePattern('pmd-refset1'),
            [],
            $this->ruleSetFactory->createRuleSets('pmd-refset1'),
            new Report()
        );

        static::assertFalse($phpmd->hasErrors());
        static::assertTrue($phpmd->hasViolations());

        // Process with exclusions, should result in no violations.
        $phpmd->processFiles(
            self::createFileUri('sourceExcluded/'),
            $this->ruleSetFactory->getIgnorePattern('exclude-pattern'),
            [],
            $this->ruleSetFactory->createRuleSets('exclude-pattern'),
            new Report()
        );

        static::assertFalse($phpmd->hasErrors());
        static::assertFalse($phpmd->hasViolations());
    }
}
