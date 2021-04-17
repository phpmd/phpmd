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

use PHPMD\AbstractTest;

class IfStatementAssignmentTest extends AbstractTest
{
    public function testRuleNotAppliesInsideClosure()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesInsideClosureCallbacks()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToIfsWithoutAssignment()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToIfsWithConditionsOnly()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleNotAppliesToLogicalOperators()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithNoViolation());
        $rule->apply($this->getMethod());
    }

    public function testRuleWorksCorrectlyWhenExpressionContainsMath()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getMethod());
    }

    public function testRuleAppliesToFunctions()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesMultipleIfConditions()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportWithOneViolation());
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesToMultilevelIfConditions()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(6));
        $rule->apply($this->getFunction());
    }

    public function testRuleAppliesMultipleTimesInOneIfCondition()
    {
        $rule = new IfStatementAssignment();
        $rule->setReport($this->getReportMock(3));
        $rule->apply($this->getFunction());
    }
}
