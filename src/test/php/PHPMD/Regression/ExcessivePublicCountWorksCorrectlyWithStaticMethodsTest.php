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
use PHPMD\Report;
use PHPMD\RuleSetFactory;

/**
 * Regression test for issue 409.
 *
 * @link https://github.com/phpmd/phpmd/issues/409
 */
class ExcessivePublicCountWorksCorrectlyWithStaticMethodsTest extends AbstractTest
{
    /**
     * @var string Beginning of the violation message
     */
    const VIOLATION_MESSAGE = 'The class ExcessivePublicCountWorksForPublicStaticMethods has 71 public methods';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\PHPMD\Renderer\TextRenderer
     */
    private $renderer;

    /**
     * Sets up the renderer mock
     */
    public function setUp()
    {
        $this->renderer = $this->getMockBuilder('PHPMD\Renderer\TextRenderer')
            ->disableOriginalConstructor()
            ->setMethods(array('renderReport', 'start', 'end'))
            ->getMock();
    }

    /**
     * testReportIsGeneratedIWithNoSuppression
     *
     * This scenario should trigger at least four violations (in this direction):
     * - ExcessivePublicCount
     * - TooManyMethods
     * - TooManyPublicMethods
     * - ExcessiveClassComplexity
     *
     * @return void
     */
    public function testReportIsGeneratedIWithNoSuppression()
    {
        self::changeWorkingDirectory();
        $phpmd = new PHPMD();
        $self = $this;
        $this->renderer->expects($this->once())
            ->method('renderReport')
            ->will(
                $this->returnCallback(
                    function (Report $report) use ($self) {
                        $isViolating = false;
                        foreach ($report->getRuleViolations() as $ruleViolation) {
                            if (strpos($ruleViolation->getDescription(), $self::VIOLATION_MESSAGE) === 0) {
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
            'codesize',
            array($this->renderer),
            new RuleSetFactory()
        );
    }

    /**
     * testReportIsNotGeneratedIWithSuppression
     *
     * This scenario should trigger at least four violations (in this direction):
     * - TooManyMethods
     * - TooManyPublicMethods
     * - ExcessiveClassComplexity
     *
     * @return void
     */
    public function testReportIsNotGeneratedIWithSuppression()
    {
        self::changeWorkingDirectory();
        $phpmd = new PHPMD();
        $self = $this;
        $this->renderer->expects($this->once())
            ->method('renderReport')
            ->will(
                $this->returnCallback(
                    function (Report $report) use ($self) {
                        $isViolating = false;
                        foreach ($report->getRuleViolations() as $ruleViolation) {
                            if (strpos($ruleViolation->getDescription(), $self::VIOLATION_MESSAGE) === 0) {
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
            'codesize',
            array($this->renderer),
            new RuleSetFactory()
        );
    }
}
