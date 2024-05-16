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

class IfStatementAssignmentTest extends AbstractTestCase
{
    public function testRuleNotAppliesInsideClosure(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesInsideClosureCallbacks(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToIfsWithoutAssignment(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToIfsWithConditionsOnly(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToLogicalOperators(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleWorksCorrectlyWhenExpressionContainsMath(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToFunctions(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesMultipleIfConditions(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToMultilevelIfConditions(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(6));
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesMultipleTimesInOneIfCondition(): void
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }
}
