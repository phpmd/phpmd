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
 *
 * @link http://phpmd.org/
 */

namespace PHPMD\Rule\Design;

use PHPMD\AbstractTestCase;

/**
 * Test case for the {@link \PHPMD\Rule\Design\EvalExpression} class.
 *
 * @covers \PHPMD\Rule\Design\EvalExpression
 */
class EvalExpressionTest extends AbstractTestCase
{
    /**
     * testRuleNotAppliesToMethodWithoutEvalExpression
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithoutEvalExpression()
    {
        $rule = new EvalExpression();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithEvalExpression
     *
     * @return void
     */
    public function testRuleAppliesToMethodWithEvalExpression()
    {
        $rule = new EvalExpression();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesMultipleTimesToMethodWithEvalExpression
     *
     * @return void
     */
    public function testRuleAppliesMultipleTimesToMethodWithEvalExpression()
    {
        $rule = new EvalExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFunctionWithoutEvalExpression
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithoutEvalExpression()
    {
        $rule = new EvalExpression();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithEvalExpression
     *
     * @return void
     */
    public function testRuleAppliesToFunctionWithEvalExpression()
    {
        $rule = new EvalExpression();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesMultipleTimesToFunctionWithEvalExpression
     *
     * @return void
     */
    public function testRuleAppliesMultipleTimesToFunctionWithEvalExpression()
    {
        $rule = new EvalExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }
}
