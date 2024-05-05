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
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Test case for the main PHPMD class.
 *
 * @covers \PHPMD\PHPMD
 */
class PHPMDTest extends AbstractTestCase
{
    /** @var RuleSetFactory */
    private $ruleSetFactory;

    protected function setUp(): void
    {
        $this->ruleSetFactory = new RuleSetFactory();
    }

    /**
     * Tests the main PHPMD interface with default settings an a xml-renderer.
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
     */
    public function testHasErrorsReturnsFalseByDefault(): void
    {
        $phpmd = new PHPMD();
        $this->assertFalse($phpmd->hasErrors());
    }

    /**
     * testHasViolationsReturnsFalseByDefault
     */
    public function testHasViolationsReturnsFalseByDefault(): void
    {
        $phpmd = new PHPMD();
        $this->assertFalse($phpmd->hasViolations());
    }

    /**
     * testHasViolationsReturnsFalseForSourceWithoutViolations
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

        $this->assertFalse($phpmd->hasErrors());
        $this->assertFalse($phpmd->hasViolations());
    }

    /**
     * testHasViolationsReturnsTrueForSourceWithViolation
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

        $this->assertFalse($phpmd->hasErrors());
        $this->assertTrue($phpmd->hasViolations());
    }

    public function testHasViolationsReturnsFalseWhenViolationIsBaselined(): void
    {
        self::changeWorkingDirectory();

        /** @var BaselineSet|PHPUnit_Framework_MockObject_MockObject $baselineSet */
        $baselineSet = $this->getMockFromBuilder(
            $this->getMockBuilder(BaselineSet::class)->disableOriginalConstructor()
        );
        $baselineSet->expects(static::exactly(2))->method('contains')->willReturn(true);

        $renderer = new XMLRenderer();
        $renderer->setWriter(new WriterStub());

        $phpmd = new PHPMD();
        $phpmd->processFiles(
            self::createFileUri('source/source_with_npath_violation.php'),
            $this->ruleSetFactory->getIgnorePattern('pmd-refset1'),
            [$renderer],
            $this->ruleSetFactory->createRuleSets('pmd-refset1'),
            new Report(new BaselineValidator($baselineSet, BaselineMode::NONE))
        );

        static::assertFalse($phpmd->hasViolations());
    }

    /**
     * testHasErrorsReturnsTrueForSourceWithError
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

        $this->assertTrue($phpmd->hasErrors());
        $this->assertFalse($phpmd->hasViolations());
    }

    /**
     * testIgnorePattern
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

        $this->assertFalse($phpmd->hasErrors());
        $this->assertTrue($phpmd->hasViolations());

        // Process with exclusions, should result in no violations.
        $phpmd->processFiles(
            self::createFileUri('sourceExcluded/'),
            $this->ruleSetFactory->getIgnorePattern('exclude-pattern'),
            [],
            $this->ruleSetFactory->createRuleSets('exclude-pattern'),
            new Report()
        );

        $this->assertFalse($phpmd->hasErrors());
        $this->assertFalse($phpmd->hasViolations());
    }
}
