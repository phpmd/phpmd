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

namespace PHPMD\Regression;

use PHPMD\PHPMD;
use PHPMD\Renderer\TextRenderer;
use PHPMD\Report;
use PHPMD\RuleSetFactory;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Regression test for UnusedSuppressWarnings feature.
 *
 * Verifies that PHPMD reports unused @SuppressWarnings annotations/attributes
 * and does not report used ones.
 */
class UnusedSuppressWarningsRegressionTest extends AbstractRegressionTestCase
{
    /** @var MockObject&TextRenderer */
    private $renderer;

    protected function setUp(): void
    {
        $this->renderer = $this->getMockBuilder(TextRenderer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['renderReport', 'start', 'end'])
            ->getMock();
    }

    /**
     * Tests that an unused #[SuppressWarnings] attribute is reported.
     */
    public function testUnusedAttributeSuppressionIsReported(): void
    {
        self::changeWorkingDirectory();
        $phpmd = new PHPMD();
        $self = $this;
        $ruleSetFactory = new RuleSetFactory();

        $this->renderer->expects(static::once())
            ->method('renderReport')
            ->willReturnCallback(
                function (Report $report) use ($self): void {
                    $hasUnusedSuppression = false;
                    foreach ($report->getRuleViolations() as $ruleViolation) {
                        if (str_contains($ruleViolation->getDescription(), 'Unused @SuppressWarnings')) {
                            $hasUnusedSuppression = true;

                            break;
                        }
                    }
                    $self->assertTrue($hasUnusedSuppression, 'Expected an "Unused @SuppressWarnings" violation');
                }
            );

        $phpmd->processFiles(
            [__DIR__ . '/Sources/UnusedSuppressWarningsOnMethod.php'],
            $ruleSetFactory->getExcludePatterns(['codesize']),
            [$this->renderer],
            $ruleSetFactory->createRuleSets(['codesize']),
            new Report()
        );
    }

    /**
     * Tests that an unused @SuppressWarnings annotation is reported.
     */
    public function testUnusedAnnotationSuppressionIsReported(): void
    {
        self::changeWorkingDirectory();
        $phpmd = new PHPMD();
        $self = $this;
        $ruleSetFactory = new RuleSetFactory();

        $this->renderer->expects(static::once())
            ->method('renderReport')
            ->willReturnCallback(
                function (Report $report) use ($self): void {
                    $hasUnusedSuppression = false;
                    foreach ($report->getRuleViolations() as $ruleViolation) {
                        if (str_contains($ruleViolation->getDescription(), 'Unused @SuppressWarnings')) {
                            $hasUnusedSuppression = true;

                            break;
                        }
                    }
                    $self->assertTrue($hasUnusedSuppression, 'Expected an "Unused @SuppressWarnings" violation');
                }
            );

        $phpmd->processFiles(
            [__DIR__ . '/Sources/UnusedSuppressWarningsAnnotationOnMethod.php'],
            $ruleSetFactory->getExcludePatterns(['codesize']),
            [$this->renderer],
            $ruleSetFactory->createRuleSets(['codesize']),
            new Report()
        );
    }

    /**
     * Tests that a used #[SuppressWarnings] attribute is NOT reported as unused.
     */
    public function testUsedSuppressionIsNotReported(): void
    {
        self::changeWorkingDirectory();
        $phpmd = new PHPMD();
        $self = $this;
        $ruleSetFactory = new RuleSetFactory();

        $this->renderer->expects(static::once())
            ->method('renderReport')
            ->willReturnCallback(
                function (Report $report) use ($self): void {
                    foreach ($report->getRuleViolations() as $ruleViolation) {
                        $self->assertStringNotContainsString(
                            'Unused @SuppressWarnings',
                            $ruleViolation->getDescription(),
                            'Did not expect an "Unused @SuppressWarnings" violation'
                        );
                    }
                }
            );

        $phpmd->processFiles(
            [__DIR__ . '/Sources/UsedSuppressWarningsOnClass.php'],
            $ruleSetFactory->getExcludePatterns(['codesize']),
            [$this->renderer],
            $ruleSetFactory->createRuleSets(['codesize']),
            new Report()
        );
    }
}
