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

namespace PHPMD\Rule\Design;

use PHPMD\AbstractTestCase;
use Throwable;

/**
 * Test case for the {@link \PHPMD\Rule\Design\ExitExpression} class.
 *
 * @covers \PHPMD\Rule\Design\ExitExpression
 */
class ExitExpressionTest extends AbstractTestCase
{
    /**
     * testRuleNotAppliesToMethodWithoutExitExpression
     * @throws Throwable
     */
    public function testRuleNotAppliesToMethodWithoutExitExpression(): void
    {
        $rule = new ExitExpression();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithExitExpression
     * @throws Throwable
     */
    public function testRuleAppliesToMethodWithExitExpression(): void
    {
        $rule = new ExitExpression();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesMultipleTimesToMethodWithExitExpression
     * @throws Throwable
     */
    public function testRuleAppliesMultipleTimesToMethodWithExitExpression(): void
    {
        $rule = new ExitExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFunctionWithoutExitExpression
     * @throws Throwable
     */
    public function testRuleNotAppliesToFunctionWithoutExitExpression(): void
    {
        $rule = new ExitExpression();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithExitExpression
     * @throws Throwable
     */
    public function testRuleAppliesToFunctionWithExitExpression(): void
    {
        $rule = new ExitExpression();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesMultipleTimesToFunctionWithExitExpression
     * @throws Throwable
     */
    public function testRuleAppliesMultipleTimesToFunctionWithExitExpression(): void
    {
        $rule = new ExitExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }
}
