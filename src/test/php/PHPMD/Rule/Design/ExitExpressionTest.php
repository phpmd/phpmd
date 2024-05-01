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

/**
 * Test case for the {@link \PHPMD\Rule\Design\ExitExpression} class.
 *
 * @covers \PHPMD\Rule\Design\ExitExpression
 */
class ExitExpressionTest extends AbstractTestCase
{
    /**
     * testRuleNotAppliesToMethodWithoutExitExpression
     *
     * @return void
     */
    public function testRuleNotAppliesToMethodWithoutExitExpression()
    {
        $rule = new ExitExpression();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesToMethodWithExitExpression
     *
     * @return void
     */
    public function testRuleAppliesToMethodWithExitExpression()
    {
        $rule = new ExitExpression();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleAppliesMultipleTimesToMethodWithExitExpression
     *
     * @return void
     */
    public function testRuleAppliesMultipleTimesToMethodWithExitExpression()
    {
        $rule = new ExitExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * testRuleNotAppliesToFunctionWithoutExitExpression
     *
     * @return void
     */
    public function testRuleNotAppliesToFunctionWithoutExitExpression()
    {
        $rule = new ExitExpression();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesToFunctionWithExitExpression
     *
     * @return void
     */
    public function testRuleAppliesToFunctionWithExitExpression()
    {
        $rule = new ExitExpression();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * testRuleAppliesMultipleTimesToFunctionWithExitExpression
     *
     * @return void
     */
    public function testRuleAppliesMultipleTimesToFunctionWithExitExpression()
    {
        $rule = new ExitExpression();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }
}
