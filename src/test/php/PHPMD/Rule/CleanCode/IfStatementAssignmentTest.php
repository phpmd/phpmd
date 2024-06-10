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

namespace PHPMD\Rule\CleanCode;

use PHPMD\AbstractTestCase;
use Throwable;

class IfStatementAssignmentTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    public function testRuleNotAppliesInsideClosure(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleNotAppliesInsideClosureCallbacks(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleNotAppliesToIfsWithoutAssignment(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleNotAppliesToIfsWithConditionsOnly(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleNotAppliesToLogicalOperators(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleWorksCorrectlyWhenExpressionContainsMath(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    /**
     * @throws Throwable
     */
    public function testRuleAppliesToFunctions(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * @throws Throwable
     */
    public function testRuleAppliesMultipleIfConditions(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    /**
     * @throws Throwable
     */
    public function testRuleAppliesToMultilevelIfConditions(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(6));
        $rule->apply($this->getFunction());
    }

    /**
     * @throws Throwable
     */
    public function testRuleAppliesMultipleTimesInOneIfCondition(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }
}
