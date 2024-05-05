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
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Regression test for issue 409.
 *
 * @link https://github.com/phpmd/phpmd/issues/409
 */
class ExcessivePublicCountWorksCorrectlyWithStaticMethodsRegressionTest extends AbstractRegressionTestCase
{
    /**
     * @var string Beginning of the violation message
     */
    private const VIOLATION_MESSAGE = 'The class ExcessivePublicCountWorksForPublicStaticMethods has 71 public methods';

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|TextRenderer
     */
    private $renderer;

    /**
     * Sets up the renderer mock
     */
    protected function setUp(): void
    {
        $this->renderer = $this->getMockFromBuilder(
            $this->getMockBuilder(TextRenderer::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['renderReport', 'start', 'end'])
        );
    }

    /**
     * testReportIsGeneratedIWithNoSuppression
     *
     * This scenario should trigger at least four violations (in this direction):
     * - ExcessivePublicCount
     * - TooManyMethods
     * - TooManyPublicMethods
     * - ExcessiveClassComplexity
     */
    public function testReportIsGeneratedIWithNoSuppression(): void
    {
        self::changeWorkingDirectory();
        $phpmd = new PHPMD();
        $self = $this;
        $ruleSetFactory = new RuleSetFactory();

        $this->renderer->expects($this->once())
            ->method('renderReport')
            ->will(
                $this->returnCallback(
                    function (Report $report) use ($self): void {
                        $isViolating = false;
                        foreach ($report->getRuleViolations() as $ruleViolation) {
                            if (str_starts_with($ruleViolation->getDescription(), $self::VIOLATION_MESSAGE)) {
                                $isViolating = true;
                                break;
                            }
                        }
                        $self->assertTrue($isViolating);
                        $self->assertEquals(4, count($report->getRuleViolations()));
                    }
                )
            );

        $phpmd->processFiles(
            __DIR__ . '/Sources/ExcessivePublicCountWorksForPublicStaticMethods.php',
            $ruleSetFactory->getIgnorePattern('codesize'),
            [$this->renderer],
            $ruleSetFactory->createRuleSets('codesize'),
            new Report()
        );
    }

    /**
     * testReportIsNotGeneratedIWithSuppression
     *
     * This scenario should trigger at least four violations (in this direction):
     * - TooManyMethods
     * - TooManyPublicMethods
     * - ExcessiveClassComplexity
     */
    public function testReportIsNotGeneratedIWithSuppression(): void
    {
        self::changeWorkingDirectory();
        $phpmd = new PHPMD();
        $self = $this;
        $ruleSetFactory = new RuleSetFactory();

        $this->renderer->expects($this->once())
            ->method('renderReport')
            ->will(
                $this->returnCallback(
                    function (Report $report) use ($self): void {
                        $isViolating = false;
                        foreach ($report->getRuleViolations() as $ruleViolation) {
                            if (str_starts_with($ruleViolation->getDescription(), $self::VIOLATION_MESSAGE)) {
                                $isViolating = true;
                                break;
                            }
                        }
                        $self->assertFalse($isViolating);
                        $self->assertEquals(3, count($report->getRuleViolations()));
                    }
                )
            );
        $phpmd->processFiles(
            __DIR__ . '/Sources/ExcessivePublicCountSuppressionWorksForPublicStaticMethods.php',
            $ruleSetFactory->getIgnorePattern('codesize'),
            [$this->renderer],
            $ruleSetFactory->createRuleSets('codesize'),
            new Report()
        );
    }
}
