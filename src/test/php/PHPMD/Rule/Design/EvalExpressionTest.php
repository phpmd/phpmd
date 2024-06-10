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
 * Test case for the {@link \PHPMD\Rule\Design\EvalExpression} class.
 *
 * @covers \PHPMD\Rule\Design\EvalExpression
 */
class EvalExpressionTest extends AbstractTestCase
{
    /**
     * testRuleNotAppliesToMethodWithoutEvalExpression
     * @throws Throwable
     */
    public function testRuleNotAppliesToMethodWithoutEvalExpression(): void
    {
        $rule = new EvalExpression();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithEvalExpression
     * @throws Throwable
     */
    public function testRuleAppliesToMethodWithEvalExpression(): void
    {
        $rule = new EvalExpression();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesMultipleTimesToMethodWithEvalExpression
     * @throws Throwable
     */
    public function testRuleAppliesMultipleTimesToMethodWithEvalExpression(): void
    {
        $rule = new EvalExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFunctionWithoutEvalExpression
     * @throws Throwable
     */
    public function testRuleNotAppliesToFunctionWithoutEvalExpression(): void
    {
        $rule = new EvalExpression();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithEvalExpression
     * @throws Throwable
     */
    public function testRuleAppliesToFunctionWithEvalExpression(): void
    {
        $rule = new EvalExpression();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesMultipleTimesToFunctionWithEvalExpression
     * @throws Throwable
     */
    public function testRuleAppliesMultipleTimesToFunctionWithEvalExpression(): void
    {
        $rule = new EvalExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }
}
